<div class="message_box">
    <div class="message_box_inner">
        <form action="#" method="post" id="message">
            <ul class="menu">
                <li class="upload_image">
                    <label for="room_image_field" title="Upload Image" id="image_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <path d="M21 15l-5-5L5 21"></path>
                        </svg>
                    </label>
                    <input type="file" id="room_image_field" accept="image/*" style="display:none;">
                </li>
                <li class="setting">&nbsp;</li>
                <li class="refresh">&nbsp;</li>
                <li class="chatlog">&nbsp;</li>
                <li class="invite">&nbsp;</li>
                <li class="notification">&nbsp;</li>
                <li class="mention">&nbsp;</li>
                <li class="whisper">&nbsp;</li>
                <li class="lock">&nbsp;</li>
                <li class="sound">&nbsp;</li>
                <li class="member">&nbsp;</li>
                <li class="animation">&nbsp;</li>
                <li class="logout">
                    <button id="logout_btn" ><?php e(t("EXIT")) ?></button>
                </li>
            </ul>
            <h2><span id="room_name"><?php e($dura['room']['name']) ?></span> (<span id="room_total"><?php e(count($dura['room']['users'])) ?></span>/<span id="room_capacity"><?php e($dura['room']['limit']) ?></span>)</h2>

            <div class="message_area">
                <div class="user_profile">
                    <img src="<?php e($dura['user']['avatar']) ?>" alt="<?php e($dura['user']['name']) ?>" />
                    <div class="user_name" title="<?php e($dura['user']['name']) ?>"><?php e($dura['user']['name']) ?></div>
                </div>
                <!-- /.user_profile -->
                <textarea name="message"></textarea>
                <div id="counter" class="message_length"><?php e(DURA_MESSAGE_MAX_LENGTH) ?></div>
                <div class="submit">
                    <input type="submit" name="post" value="<?php e(t("POST!")) ?>" />
                </div>
            </div>
            <!-- /.message_area -->
            <?php if ( $ret = file_exists(DURA_PATH.'/js/sound.mp3') ) : ?>
            <a href="<?php echo DURA_URL ?>/js/sound.mp3" id="sound" class="hide">sound</a>
            <?php endif ?>
            <?php if ( $ret = file_exists(DURA_PATH.'/js/whisper.mp3') ) : ?>
            <a href="<?php echo DURA_URL ?>/js/whisper.mp3" id="private" class="hide">private</a>
            <?php endif ?>
            <ul id="members" class="hide">
                <?php foreach ( $dura['room']['users'] as $user  ) : ?>
                <li><?php e($user['name']) ?> <?php if ( $user['id'] == $dura['room']['host'] ) :?><?php e(t("{Host}")) ?><?php endif ?></li>
                <?php endforeach ?>
            </ul>
            <ul id="invite" class="hide">
                <li><input type="text" value="<?php e(Dura::url(null, null, array('id' => $dura['room']['id'], 'invite' => $dura['room']['invite']))) ?>" size="40" class="textbox" /></li>
                <?php foreach ( $dura['social'] as $name => $link ) : ?>
                <li><a href="<?php e(Dura::decodeHtml($link)) ?>" target="_blank" title="<?php e(t("Share this on {1}", ucfirst($name))) ?>"><img src="<?php e(DURA_URL) ?>/images/social/<?php e($name) ?>.png" width="24" height="24" alt="<?php e(ucfirst($name)) ?>" /></a></li>
                <?php endforeach ?>
            </ul>
            <ul class="hide">
                <li id="user_id"><?php e($dura['user']['id']) ?></li>
                <li id="user_name"><?php e($dura['user']['name']) ?></li>
                <li id="user_icon"><?php e($dura['user']['icon']) ?></li>
                <li id="user_code"><?php e($dura['user']['code']) ?></li>
            </ul>
        </form>
        <!-- /#message -->
        <div id="setting_pannel" class="hide">
            <div>
                <?php e(t("Room Name")) ?>&nbsp;<input type="text" name="room_name" value="<?php e($dura['room']['name']) ?>" size="20" maxlength="12" /><br />
                <?php e(t("Language")) ?>&nbsp;
                <select name="room_language">
                    <?php foreach ( $dura['languages'] as $langcode => $language ) : ?>
                    <option value="<?php e($langcode) ?>"<?php if ($langcode == $dura['room']['language'] ) : ?> selected="selected"<?php endif ?>><?php e($language) ?></option>
                    <?php endforeach ?>
                </select>
                &nbsp;<input type="button" name="save" class="right button" value="<?php e(t("Change")) ?>" /><br />
            </div>
            <hr />
            <div>
                <?php e(t("Max Members")) ?>&nbsp;
                <select id="room_limit" name="room_limit"<?php if ( $dura['limit']['min'] == 0 && $dura['limit']['max'] == 0 ) : ?> disabled="disabled"<?php endif ?>>
                    <?php if ( $dura['limit']['min'] == 0 && $dura['limit']['max'] == 0 ) : ?>
                    <option value="<?php echo $dura['room']['limit'] ?>"><?php echo $dura['room']['limit'] ?></option>
                    <?php else: ?>
                    <?php for ( $i = $dura['limit']['min']; $i <= $dura['limit']['max']; $i++ ) : ?>
                    <option value="<?php echo $i ?>"<?php if ( $dura['room']['limit'] == $i ) : ?> selected="selected"<?php endif ?>><?php echo $i ?></option>
                    <?php endfor ?>
                    <?php endif ?>
                </select>
                &nbsp;<?php e(t("{1} members", '')) ?>&nbsp;
                <input type="button" name="update" class="right button" value="<?php e(t("Update")) ?>" /><br />
            </div>
            <hr />
            <div>
                <?php e(t("Room Password")) ?>&nbsp;<input type="text" name="room_password" value="<?php e($dura['room']['password']) ?>" size="15" maxlength="25" />&nbsp;<input type="button" name="set" class="right button" value="<?php e(t("Set")) ?>" /><br />
            </div>
            <hr />
            <div>
                <?php e(t("Blocked IP")) ?>&nbsp;
                <select id="blocked_ip" name="blocked_ip">
                    <?php if ( !empty($dura['room']['bans']) ) : ?><?php foreach ( $dura['room']['bans'] as $ban ) : ?>
                    <option value="<?php e($ban['id']) ?>"><?php e(Dura::maskIP($ban['ip'])) ?></option>
                    <?php endforeach ?><?php else : ?>
                    <option value=""><?php e(t("N/A")) ?></option>
                    <?php endif ?>
                </select>
                &nbsp;<input type="button" name="remove" class="right button" value="<?php e(t("Remove")) ?>" /><br />
            </div>
            <hr />
            <div>
                <input type="button" name="handover" value="<?php e(t("Handover host")) ?>" class="button" disabled="disabled" />
                <input type="button" name="kick" value="<?php e(t("Kick user")) ?>" class="button" disabled="disabled" />
                <input type="button" name="ban" value="<?php e(t("Ban user")) ?>" class="button" disabled="disabled" />
            </div>
            <ul id="user_list"></ul>
        </div>
        <!-- /#setting_pannel -->
    </div>
    <!-- /.message_box_inner -->
</div>
<!-- /.message_box -->
<div id="talks_box">
    <div id="talks">
        <?php foreach ( $dura['room']['talks'] as $talk ) : ?>
        <?php if ( !$talk['uid'] ) : ?>
        <div class="talk system" id="<?php e($talk['id']) ?>"><?php e($talk['message']) ?></div>
        <?php else: ?>
        <dl class="talk <?php e($talk['icon']) ?>" id="<?php e($talk['id']) ?>">
    <dt><?php e($talk['name']) ?></dt>
    <dd title="<?php e($talk['time']) ?>">
        <?php if (!empty($talk['image'])): ?>
            <img src="<?php e($talk['image']) ?>" style="max-width:300px;max-height:300px;border-radius:8px;display:block;margin-left:15px;" alt="Image"/>
            <?php if (!empty($talk['message']) && $talk['message'] != '[image]'): ?>
            <div style="margin-top:6px;margin-left:15px;"><?php e($talk['message']) ?></div>
            <?php endif ?>
        <?php else: ?>
            <div class="bubble">
                <p class="body"><?php e($talk['message']) ?></p>
            </div>
        <?php endif ?>
    </dd>
</dl>
        <?php endif ?>
        <?php endforeach ?>
    </div>
    <!-- /#talks -->
</div>
<!-- /#talks_box -->
<div id="whisper_box">
    <div class="whisper_message_box">
        <div class="whisper_message_box_inner">
            <form action="#" method="post" id="whisper">
                <ul class="menu">
                    <li class="droplist">
                        <select name="recipient">
                            <?php foreach ( $dura['room']['users'] as $user  ) : ?>
                            <?php if ( $user['id'] != Dura::user()->getId() ) : ?>
                            <option value="<?php e($user['id']) ?>"><?php e($user['name']) ?></option>
                            <?php endif ?>
                            <?php endforeach ?>
                        </select>
                    </li>
                </ul>
                <h2><?php e(t("Private Mode")) ?></h2>
                <div class="message_area">
                    <textarea name="message"></textarea>
                    <div id="whister" class="message_length"><?php e(DURA_MESSAGE_MAX_LENGTH) ?></div>
                    <div class="submit">
                        <input type="submit" name="post" value="<?php e(t("POST!")) ?>" />
                    </div>
                </div>
            </form>
        </div>
        <!-- /.whisper_message_box_inner -->
    </div>
    <!-- /.whisper_message_box -->
    <div id="murmurs_box">
        <div id="murmurs">
            <?php if ( !empty($dura['room']['whispers']) ) : ?>
            <?php foreach ( $dura['room']['whispers'] as $whisper ) : ?>
            <dl class="talk <?php e($whisper['icon']) ?>" id="<?php e($whisper['id']) ?>">
                <?php if ( $whisper['code'] != '' ) : ?>
                <dt title="<?php e($whisper['code']) ?>"><?php e($whisper['name']) ?></dt>
                <?php else : ?>
                <dt><?php e($whisper['name']) ?></dt>
                <?php endif ?>
                <dd title="<?php e($whisper['time']) ?>">
                    <div class="bubble">
                        <p class="body"><?php e($whisper['message']) ?></p>
                        <span title="from" class="hide"><?php e($whisper['uid']) ?></span>
                        <span title="to" class="hide"><?php e($whisper['rid']) ?></span>
                    </div>
                </dd>
            </dl>
            <?php endforeach ?>
            <?php endif ?>
        </div>
        <!-- /#murmurs -->
    </div>
    <!-- /#murmurs_box -->
</div>
<!-- /#whisper_box -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var logoutBtn = document.getElementById('logout_btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '/room', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            window.location.reload();
                        } else {
                            alert('Logout failed.');
                        }
                    }
                };
                xhr.send('logout=1');
            });
        }
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Logout button (unchanged)
    var logoutBtn = document.getElementById('logout_btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '/room', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        window.location.reload();
                    } else {
                        alert('Logout failed.');
                    }
                }
            };
            xhr.send('logout=1');
        });
    }

    // Image upload logic:
    const fileInput = document.getElementById('room_image_field');
    const imageIcon = document.getElementById('image_icon');

    if (fileInput && imageIcon) {
        imageIcon.addEventListener('click', function(e) {
            e.preventDefault();
            fileInput.click();
        });

        fileInput.addEventListener('change', function() {
            const file = fileInput.files[0];
            if (!file) return;

            // Accept only images, max 3MB
            if (!file.type.match(/^image\//)) {
                alert('Only image files are allowed!');
                fileInput.value = '';
                return;
            }
            if (file.size > 3 * 1024 * 1024) {
                alert('Image is too large (max 3MB).');
                fileInput.value = '';
                return;
            }

            // Upload
            const formData = new FormData();
            formData.append('image', file);
            formData.append('upload_room_image', '1');

            fetch('/room', {
                method: 'POST',
                body: formData
            })
            .then(resp => resp.json())
            .then(data => {
                if (data.success && data.url) {
                    // Show image in chat instantly:
                    addImageToChat(data.url, data.id);
                } else {
                    alert(data.error || 'Image upload failed.');
                }
                fileInput.value = '';
            })
            .catch(() => {
                alert('Could not upload image.');
                fileInput.value = '';
            });
        });
    }

    // Helper: Insert image message into chat (top of chat)
    function addImageToChat(url, id) {
        const talks = document.getElementById('talks');
        if (!talks) return;

        // Use correct user info:
        const userNameElem = document.getElementById('user_name');
        const userIconElem = document.getElementById('user_icon');
        const userName = userNameElem ? userNameElem.textContent : 'You';
        const userIcon = userIconElem ? userIconElem.textContent : '';
        const now = new Date();

        // Use the same markup and class structure as polling code:
        const dl = document.createElement('dl');
        dl.className = 'talk ' + userIcon;
        dl.id = id;

        const dt = document.createElement('dt');
        dt.textContent = userName;

        const dd = document.createElement('dd');
        dd.title = now.toLocaleString();

        // No bubble wrapper for images - place image directly in dd
        const img = document.createElement('img');
        img.src = url;
        img.alt = 'Image';
        img.style.maxWidth = '300px';
        img.style.maxHeight = '300px';
        img.style.borderRadius = '8px';
        img.style.display = 'block';
        img.style.marginLeft = '15px'; // Align with bubble content

        dd.appendChild(img);

        dl.appendChild(dt);
        dl.appendChild(dd);

        // Prepend to chat (top)
        talks.insertBefore(dl, talks.firstChild);
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Delegate click for all images in chat bubbles
    document.getElementById('talks').addEventListener('click', function(e) {
        if (e.target && e.target.tagName === 'IMG') {
            const src = e.target.src;
            const lightbox = document.getElementById('image_lightbox');
            const lightboxImg = document.getElementById('lightbox_img');
            lightboxImg.src = src;
            lightbox.style.display = 'flex';
        }
    });

    // Close lightbox on background click
    document.getElementById('image_lightbox').addEventListener('click', function(e) {
        // Only close if background is clicked (not the image)
        if (e.target === this || e.target.className === 'lightbox_bg') {
            this.style.display = 'none';
            document.getElementById('lightbox_img').src = '';
        }
    });

    // Optional: close on Esc key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.getElementById('image_lightbox').style.display = 'none';
            document.getElementById('lightbox_img').src = '';
        }
    });
});

</script>


<div id="pagetop">
    <a href="#"><img src="<?php e(DURA_URL) ?>/css/pagetop.gif" alt="JUMP" /></a>
</div>

<style>
    #image_lightbox {
    position: fixed;
    top: 0; left: 0; width: 100vw; height: 100vh;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}
#image_lightbox .lightbox_bg {
    position: absolute;
    top: 0; left: 0; width: 100vw; height: 100vh;
    background: rgba(0,0,0,0.8);
}
#image_lightbox img {
    position: relative;
    max-width: 90vw;
    max-height: 90vh;
    border-radius: 10px;
    box-shadow: 0 2px 16px rgba(0,0,0,0.7);
    z-index: 1;
}

</style>

<div id="image_lightbox" style="display:none;">
    <div class="lightbox_bg"></div>
    <img id="lightbox_img" src="" alt="Large preview">
</div>
