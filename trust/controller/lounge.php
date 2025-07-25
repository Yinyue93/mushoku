<?php
/**
 * A simple description for this script
 *
 * PHP Version 5.3.0 or Upper version
 *
 * @package    Dura
 * @author     Hidehito NOZAWA aka Suin <http://suin.asia>
 * @author     schnabear
 * @copyright  2010 Hidehito NOZAWA
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3
 *
 */

class Dura_Controller_Lounge extends Dura_Abstract_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

	public function main()
	{
		$this->_validateUser();

		// Handle AJAX getLounge request
		if (isset($_GET['getLounge'])) {
            $roomHandler = new Dura_Model_RoomHandler;
            $roomModels = $roomHandler->loadAll();
            $rooms = array();
            $roomExpire = time() - DURA_CHAT_ROOM_EXPIRE;
            $activeUser = 0;
            foreach ($roomModels as $id => $roomModel) {
                if ($roomModel['update'] < $roomExpire && !$roomModel['permanent']) {
                    continue;
                }
                // Only keep active users
                $roomModel['users'] = array_filter(
                    isset($roomModel['users']) ? $roomModel['users'] : array(),
                    function($user) use ($roomExpire) {
                        return isset($user['update']) && $user['update'] >= $roomExpire;
                    }
                );
                $roomModel['users'] = array_values($roomModel['users']);
                $totalUsers = count($roomModel['users']);
                $activeUser += $totalUsers;
                // Prepare users array for frontend (id, name, icon)
                $usersArr = array();
                foreach ($roomModel['users'] as $user) {
                    $usersArr[] = array(
                        'id' => isset($user['id']) ? $user['id'] : '',
                        'name' => isset($user['name']) ? $user['name'] : '',
                        'icon' => isset($user['icon']) ? Dura_Class_Icon::getIconUrl($user['icon']) : ''
                    );
                }
                $rooms[] = array(
                    'id' => $id,
                    'name' => isset($roomModel['name']) ? $roomModel['name'] : '',
                    'users' => $usersArr,
                    'total' => $totalUsers,
                    'limit' => isset($roomModel['limit']) ? $roomModel['limit'] : 0,
                    'mark' => isset($roomModel['mark']) ? $roomModel['mark'] : '',
                    'password' => isset($roomModel['password']) ? $roomModel['password'] : '',
                    'color' => isset($roomModel['color']) ? $roomModel['color'] : '',
                    'host' => isset($roomModel['host']) ? $roomModel['host'] : '',
                    'permanent' => isset($roomModel['permanent']) ? $roomModel['permanent'] : false,
                    'unavailable' => isset($roomModel['unavailable']) ? $roomModel['unavailable'] : false
                );
            }
            header('Content-Type: application/json');
            echo json_encode([
                'online_users' => $activeUser,
                'rooms' => $rooms
            ]);
            exit;
        }

		$this->_default();
	}

	protected function _default()
	{
		$this->_redirectToRoom();

		$this->_rooms();

		$this->_profile();

		$this->_view();
	}

	protected function _redirectToRoom()
	{
		if ( Dura_Class_RoomSession::isCreated() )
		{
			Dura::redirect('room');
		}
	}

	protected function _rooms()
	{
		$roomHandler = new Dura_Model_RoomHandler;
		$roomModels = $roomHandler->loadAll();

		$rooms = array();

		$roomExpire = time() - DURA_CHAT_ROOM_EXPIRE;
		$activeUser = 0;
		$activeCapacity = 0;

		$userId = Dura::user()->getId();
		$userIP = Dura::user()->getIP();

		foreach ( $roomModels as $id => $roomModel )
		{
			if ( $roomModel['update'] < $roomExpire && !$roomModel['permanent'] )
			{
				$roomHandler->delete($id);
				continue;
			}

			$changeHost = false;
			$roomUpdate = false;

			foreach ( $roomModel['users'] as $key => $user )
			{
				if ( $user['update'] < $roomExpire )
				{
					$userName = $user['name'];

					$this->_npcDisconnect($userName, $roomModel);

					if ( $this->_isHost($user['id'], $roomModel) )
					{
						$changeHost = true;
					}

					$this->_removeWhisper($user['id'], $roomModel);

					unset($roomModel['users'][$key]);

					$roomUpdate = true;
				}
			}
			$roomModel['users'] = array_values($roomModel['users']);

			if ( $roomUpdate )
			{
				if ( $changeHost && count($roomModel['users']) && !$roomModel['permanent'] )
				{
					$this->_moveHostRight($roomModel);
				}

				if ( empty($roomModel['users']) )
				{
					$this->_npcRoomEmpty($roomModel);
				}

				$this->_weepTalk($roomModel);

				$roomHandler->save($id, $roomModel);
			}

			$roomModel['creator'] = '';
			$roomModel['total'] = 0;

			if ( !empty($roomModel['users']) )
			{
				foreach ( $roomModel['users'] as $user )
				{
					if ( $user['id'] == $roomModel['host'] )
					{
						$roomModel['creator'] = $user['name'];
					}

					if ( $user['id'] == $userId )
					{
						Dura_Class_RoomSession::create($id);
						$this->_redirectToRoom();
					}
				}

				$roomModel['total'] = count($roomModel['users']);
			}

			$roomModel['unavailable'] = false;

			if ( !empty($roomModel['bans']) )
			{
				foreach ( $roomModel['bans'] as $ban )
				{
					if ( $ban['id'] == Dura::hash($userIP) )
					{
						$roomModel['unavailable'] = true;
						break;
					}
				}
			}

			$roomModel['id'] = $id;

			$isSameLanguage = (int) ( $roomModel['language'] != Dura::user()->getLanguage() );

			$rooms[$isSameLanguage][$roomModel['language']][(int) $roomModel['permanent']][] = $roomModel();

			$activeUser += $roomModel['total'];
			$activeCapacity += $roomModel['limit'];
		}

		unset($roomHandler, $roomModels, $roomModel);

		ksort($rooms);

		// Since we do not have GROUP BY ORDER BY etc
		// We LOOP BY and STAND BY that it hopefully works
		foreach ( $rooms as $keySameLanguage => $valSameLanguage )
		{
			foreach ( $valSameLanguage as $keyLanguage => $valLanguage )
			{
				krsort($rooms[$keySameLanguage][$keyLanguage]);

				foreach ( $valLanguage as $keyPermanent => $dummy )
				{
					// usort($rooms[$keySameLanguage][$keyLanguage][$keyPermanent], array($this, '_cmpByLanguage'));
					usort(
						$rooms[$keySameLanguage][$keyLanguage][$keyPermanent],
						function ($a, $b)
						{
							if ( $a['create'] == $b['create'] )
							{
								return 0;
							}
							elseif ( $a['create'] < $b['create'] )
							{
								return 1;
							}
							else
							{
								return -1;
							}
						}
					);
				}
			}
		}

		$this->output['rooms'] = $rooms;
		$this->output['active_user'] = $activeUser;
		$this->output['active_capacity'] = $activeCapacity;
	}

	protected function _profile()
	{
		$user =& Dura::user();
		$icon = $user->getIcon();
		$icon = Dura_Class_Icon::getIconUrl($icon);

		$profile = array(
			'icon' => $icon,
			'name' => $user->getName(),
		);

		$this->output['profile'] = $profile;
	}

	protected function _moveHostRight(&$roomModel)
	{
		foreach ( $roomModel['users'] as $user )
		{
			$roomModel['host'] = $user->id;
			$nextHost = $user->name;
			break;
		}

		$this->_npcNewHost($nextHost, $roomModel);
	}

	protected function _isHost($userId, $roomModel)
	{
		return ( $userId == $roomModel['host'] );
	}

	protected function _npcDisconnect($userName, &$roomModel)
	{
		$talk = array();
		$talk['id'] = md5(microtime().mt_rand());
		$talk['uid'] = '';
		$talk['name'] = $userName;
		$talk['message'] = "{1} lost the connection.";
		$talk['icon'] = '';
		$talk['time'] = microtime(true);

		$roomModel['talks'][] = $talk;
	}

	protected function _npcNewHost($userName, &$roomModel)
	{
		$talk = array();
		$talk['id'] = md5(microtime().mt_rand());
		$talk['uid'] = '';
		$talk['name'] = $userName;
		$talk['message'] = "{1} is a new host.";
		$talk['icon'] = '';
		$talk['time'] = microtime(true);

		$roomModel['talks'][] = $talk;
	}

	protected function _npcRoomEmpty(&$roomModel)
	{
		$talk = array();
		$talk['id'] = md5(microtime().mt_rand());
		$talk['uid'] = '';
		$talk['name'] = '';
		$talk['message'] = "No users in chat.";
		$talk['icon'] = '';
		$talk['time'] = microtime(true);

		$roomModel['talks'][] = $talk;
	}

	protected function _weepTalk(&$roomModel)
	{
		while ( count($roomModel['talks']) > DURA_LOG_LIMIT )
		{
			array_shift($roomModel['talks']);
		}
	}

	protected function _removeWhisper($userId, &$roomModel)
	{
		foreach ( $roomModel['whispers'] as $key => $whisper )
		{
			if ( $whisper['uid'] == $userId || $whisper['rid'] == $userId )
			{
				unset($roomModel['whispers'][$key]);
			}
		}

        if ( !empty($roomModel['whispers']) )
        {
            $roomModel['whispers'] = array_values($roomModel['whispers']);
        }
        else
        {
            $roomModel['whispers'] = [];
        }
	}
}
