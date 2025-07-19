# MUSHOKU (無色) - Real-Time Multi-Language Chat Application

**MUSHOKU** (無色, meaning "colorless" in Japanese) is a sophisticated real-time chat application designed for anonymous group conversations. Originally created for the N33T (NEET) Group and first launched on June 21, 2010, MUSHOKU was completely rewritten in April 2012 based on the DRRR-Like-Chat (DLC) framework by SUIN.

The name "MUSHOKU" reflects the anonymous nature of its users - colorless and without predetermined identity, allowing for genuine, unfiltered communication.

## 🌟 Key Features

### 💬 **Real-Time Chat System**
- **Multi-room support** with dynamic room creation and management
- **Real-time messaging** using Ajax/Comet long polling for instant updates
- **Message history** with configurable log limits (default: 50 messages)
- **140-character message limit** (Twitter-style, configurable)

### 🌍 **Multi-Language Support**
Full internationalization with 16 language packs:
- 🇺🇸 **English** (en-US)
- 🇯🇵 **Japanese** (ja-JP) 
- 🇷🇺 **Russian** (ru-RU)
- 🇨🇳 **Chinese Simplified** (zh-CN)
- 🇹🇼 **Chinese Traditional** (zh-TW)
- 🇩🇪 **German** (de-DE)
- 🇪🇸 **Spanish** (es-ES)
- 🇮🇹 **Italian** (it-IT)
- 🇰🇷 **Korean** (ko-KR)
- 🇵🇱 **Polish** (pl-PL)
- 🇧🇷 **Portuguese** (pt-BR)
- 🇵🇭 **Filipino** (tl-PH)
- 🇹🇷 **Turkish** (tr-TR)
- 🇮🇱 **Hebrew** (he-IL)
- 🇮🇩 **Indonesian** (id-ID)
- 🌍 **Esperanto** (eo)

### 👥 **User & Room Management**
- **Anonymous login** with customizable avatars (20+ icon choices)
- **Tripcode system** for consistent anonymous identity (`username#password`)
- **Room capacity management** (2-12 users per room, configurable)
- **Password-protected rooms** for private conversations
- **Permanent rooms** (admin-only feature)
- **Room themes** with custom colors and marks
- **Auto-cleanup** of inactive rooms and users

### 🔐 **Advanced Security & Administration**
- **Admin panel** with full user and room management
- **User moderation**: kick, ban, and IP blocking capabilities
- **Host privileges** with automatic host transfer system
- **CSRF protection** and XSS prevention
- **reCAPTCHA integration** for spam prevention
- **IP-based user identification** for consistent identity

### 💌 **Private Messaging System**
- **Whisper functionality** for private messages within rooms
- **Recipient targeting** with user selection interface
- **Whisper notifications** with distinct visual and audio cues
- **Privacy controls** ensuring messages only reach intended recipients

### 🔊 **Rich User Experience**
- **Sound notifications** for messages and whispers
- **Desktop notifications** (browser-dependent)
- **Visual themes** with bubble-style message display
- **Responsive interface** with mobile-friendly design
- **Animated elements** with toggle controls
- **Real-time user list** showing online members and host status

### 🔗 **Social Features**
- **Room sharing** with social media integration
- **Invite system** with unique invite codes to bypass passwords
- **Room browsing** with live user counts and activity status
- **Multi-language room separation** for better user experience

## 🏗️ Technical Architecture

### **Backend**
- **PHP 5.3+** with custom MVC framework (Dura)
- **File-based storage** using JSON for rooms and messages
- **Session management** with configurable timeouts
- **Modular controller system** for clean code organization
- **Template engine** for dynamic content rendering

### **Frontend** 
- **jQuery-powered** real-time interface
- **SoundManager2** for cross-browser audio support
- **Ajax/Comet** long polling for live updates
- **Responsive CSS3** with modern web standards
- **Cross-browser compatibility** including legacy IE support

### **Storage**
- **No database required** - perfect for shared hosting
- **JSON-based room storage** in `/trust/storage/`
- **Atomic file operations** preventing data corruption
- **Automatic cleanup** of expired content

## 📋 System Requirements

- **PHP 5.3** or higher
- **php-curl** extension
- **php-mbstring** extension for multi-byte string handling
- **Web server** with mod_rewrite support (Apache/Nginx)
- **File write permissions** for storage directory

## 🚀 Installation

1. **Clone or download** the MUSHOKU source code
2. **Configure web server** to point to the project directory
3. **Copy configuration**: `cp setting.dist.php setting.php`
4. **Edit settings** in `setting.php`:
   ```php
   // Admin credentials
   $duraAdmin = array(
       array('name' => 'YourAdmin', 'pass' => 'YourPassword')
   );
   
   // Site configuration
   define('DURA_TITLE', 'Your Site Name');
   define('DURA_SUBTITLE', 'Your Subtitle');
   ```
5. **Set permissions** for the `/trust/storage/` directory (755 or 777)
6. **Access your site** and test functionality

For detailed installation instructions, refer to the original [DRRR-Like-Chat documentation](https://github.com/schnabear/drrr-like-chat).

## ⚙️ Configuration Options

MUSHOKU offers extensive customization through `setting.php`:

### **Chat Behavior**
```php
define('DURA_LOG_LIMIT', 50);              // Messages per room
define('DURA_MESSAGE_MAX_LENGTH', 140);    // Character limit
define('DURA_USER_MIN', 2);                // Minimum users per room
define('DURA_USER_MAX', 12);               // Maximum users per room
define('DURA_CHAT_ROOM_EXPIRE', 1800);    // Room timeout (seconds)
```

### **Security Settings**
```php
define('DURA_USE_RECAPTCHA', true);        // Enable CAPTCHA
define('RECAPTCHA_PUBLIC_KEY', 'your_key'); 
define('RECAPTCHA_PRIVATE_KEY', 'your_key');
```

### **Performance Tuning**
```php
define('DURA_USE_COMET', 1);               // Long polling
define('DURA_SLEEP_LOOP', 3);              // Polling frequency
define('DURA_SITE_USER_CAPACITY', 500);    // Total site capacity
```

## 🎮 Usage Guide

### **For Users**
1. **Select avatar** and enter a username
2. **Choose language** from the dropdown
3. **Join existing room** or create a new one
4. **Use tripcodes** for persistent identity: `username#secretkey`
5. **Send whispers** by clicking user icons
6. **Toggle features** using the control panel (sound, notifications, etc.)

### **For Administrators**
1. **Access admin panel** at `/admin`
2. **Create permanent rooms** with custom themes
3. **Broadcast announcements** to all rooms
4. **Moderate users** with kick/ban functionality
5. **Monitor site activity** through the lounge interface

## 🤝 Contributing

This project maintains its **decade-old functionality** and **IE compatibility** as core principles. Contributions are welcome for:

- **Bug fixes** and security improvements
- **Translation additions** or corrections
- **Documentation** enhancements
- **Performance** optimizations that don't break compatibility

**Note**: Feature requests that alter core functionality may be declined to preserve the classic experience.

## 📜 History & Legacy

MUSHOKU has a rich history spanning over a decade:

- **2010**: Original launch as private chat for N33T Group
- **2012**: Complete rewrite based on DLC framework
- **2010-2024**: Continuous operation maintaining retro compatibility
- **Community**: Served thousands of users across multiple languages

The application represents a snapshot of early 2010s web technology while remaining functional and secure for modern use.

## 📄 License

Licensed under **GNU General Public License v3.0**

Original DRRR-Like-Chat framework by [SUIN](http://suin.asia)  
MUSHOKU modifications and maintenance by schnabear

---

*"The word MUSHOKU means colorless and since most of its users were anonymous, the name MUSHOKU is suited to be used."*

**Experience authentic anonymous chat culture from the golden age of internet communities.**
