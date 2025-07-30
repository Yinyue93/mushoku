# MUSHOKU Chat Application - AI Assistant Guide

## Quick Start Commands

```bash
# Check if site is running
curl http://localhost/

# Test PHP-FPM
php-fpm -t

# Check nginx configuration
nginx -t

# Restart services
service php-fpm restart
service nginx restart
```

## Project Overview

MUSHOKU is a PHP-based real-time chat application using Ajax long polling (NOT WebSockets). Originally from 2010, rewritten in 2012, it's a legacy codebase with significant technical debt but remains functional.

## Key Architecture Points

### Backend
- **Framework**: Custom MVC called "Dura" 
- **PHP Version**: 5.3+ (legacy)
- **Storage**: JSON files in `/trust/storage/` (NO DATABASE)
- **Entry Point**: `index.php` → `dura.php` → MVC routing

### Frontend
- **Real-time**: Ajax long polling via `jquery.chat.js`
- **jQuery**: Legacy version (pre-1.9) with deprecated features
- **Polling**: Checks file hash changes every 3 seconds

### Real-time Mechanism
1. Client polls `/room/ajax/` endpoint
2. Server checks JSON file hash: `hash_file('crc32b', $file)`
3. If unchanged, server sleeps for up to 3 iterations
4. On change, returns updated room data as JSON

## Critical Files

### Configuration
- `setting.php` (copy from `setting.dist.php`) - Main config
- Admin credentials: `$duraAdmin` array in settings

### Core Application
- `index.php` - Entry point
- `dura.php` - Bootstrap and routing
- `/trust/controller/room.php` - Chat room logic
- `/trust/controller/room.php::_ajax()` - Long polling handler

### Frontend
- `/js/jquery.chat.js` - Main chat client (1,814 lines!)
- `/js/jquery.sound.js` - Audio notifications
- `/js/jquery.notification.js` - Desktop notifications

### Storage
- `/trust/storage/room_*.json` - Chat room data
- Needs write permissions (755 or 777)

## Nginx Configuration Requirements

```nginx
# Key requirements:
# 1. PHP-FPM pass for .php files
# 2. URL rewriting for MVC routes
# 3. Security headers
# 4. Proper index handling

location / {
    try_files $uri $uri/ /index.php?$args;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
}

# Block access to trust directory
location ~ ^/trust {
    deny all;
}
```

## Known Issues & Workarounds

### 1. File Permissions
```bash
chmod -R 755 /var/www/html/trust/storage/
chown -R www-data:www-data /var/www/html/trust/storage/
```

### 2. PHP Extensions Required
- curl
- mbstring
- session

### 3. URL Rewriting
The app expects either:
- `/index.php?controller=room&action=ajax` 
- `/room/ajax/` (with rewrite)

### 4. Session Issues
- Check session.save_path is writable
- Session name: `DURASESS`

## Security Considerations

1. **Admin Credentials**: Plain text in `setting.php` - consider environment variables
2. **CSRF Token**: Uses `DURA_SECURE_KEY` - should be randomized
3. **File Storage**: Vulnerable to race conditions under high load
4. **No Rate Limiting**: Consider adding nginx rate limits

## Performance Tuning

### PHP-FPM
```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
```

### Key Settings in setting.php
```php
define('DURA_SLEEP_LOOP', 3);        // Polling cycles
define('DURA_SLEEP_TIME', 1);        // Seconds per cycle  
define('DURA_LOG_LIMIT', 50);        // Messages per room
define('DURA_SITE_USER_CAPACITY', 500);
```

## Debugging Tips

1. **Check Ajax Endpoint**: Visit `/room/ajax/` - should return JSON error
2. **File Storage**: Check `/trust/storage/` has JSON files after room creation
3. **PHP Errors**: `tail -f /var/log/php-fpm/error.log`
4. **Nginx Errors**: `tail -f /var/log/nginx/error.log`

## Technical Debt Summary

### Critical Issues
- Monolithic 1,814-line `jquery.chat.js`
- Uses deprecated `$.browser.msie`
- 40+ global variables in main chat script
- No error logging or proper error handling
- Empty catch blocks throughout

### jQuery Anti-patterns
- No selector caching
- DOM thrashing in loops
- Memory leaks from unremoved handlers
- Callback hell in polling logic

## Deployment Checklist

- [ ] Copy `setting.dist.php` to `setting.php`
- [ ] Configure admin credentials in `setting.php`
- [ ] Set up nginx with PHP-FPM pass
- [ ] Configure URL rewriting
- [ ] Set file permissions on `/trust/storage/`
- [ ] Install PHP extensions: curl, mbstring
- [ ] Test Ajax endpoint responds
- [ ] Verify session directory is writable
- [ ] Check PHP-FPM pool settings
- [ ] Monitor file I/O performance

## Testing the Application

1. **Basic Test**: Load homepage, should see login form
2. **Create Room**: Enter username, select avatar, create room
3. **Chat Test**: Send message, should appear immediately
4. **Polling Test**: Open browser console, watch for Ajax requests every ~3 seconds
5. **Multi-user**: Open in two browsers, messages should sync

## Common Errors

### "Server error" on message send
- Check `/trust/storage/` permissions
- Verify PHP session is working
- Check nginx error logs

### Room not updating
- Verify Ajax endpoint is accessible
- Check for JavaScript errors in console
- Ensure file hash checking works

### Can't create rooms
- Storage directory not writable
- PHP session issues
- CSRF token mismatch