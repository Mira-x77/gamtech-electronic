# GamTech Language & Theme Toggle Guide

## Overview

The GamTech website now supports **French language** and **Dark/Light theme toggle**. These features are fully integrated into the header and stored in browser cookies for persistent user preferences.

---

## Features

### 1. Language Switcher
- **Location**: Top header bar, next to the search and cart buttons
- **Languages Supported**: 
  - English (EN) - Default
  - Français (FR)
- **Storage**: Browser cookie (`gt_lang`) - persists for 365 days
- **How it works**:
  - Click the language button (shows "EN" or "FR")
  - A dropdown menu appears with both language options
  - Select your preferred language
  - The entire site UI updates to French or English
  - Your preference is saved and remembered on future visits

### 2. Dark/Light Theme Toggle
- **Location**: Top header bar, left of the language button
- **Features**:
  - Sun icon = Light mode available (currently in Dark mode)
  - Moon icon = Dark mode available (currently in Light mode)
  - Click to toggle between themes
- **Storage**: Browser localStorage (`gs-theme`) - persists for the session and future visits
- **Color Schemes**:
  - **Dark Mode** (Default):
    - Background: `#0f0f0f`
    - Cards: `#181818`
    - Text: `#d4d4d8`
  - **Light Mode**:
    - Background: `#ffffff`
    - Cards: `#fafafa`
    - Text: `#333333`

---

## Translated Elements

The following site elements are translated to French:

| Element | English | French |
|---------|---------|--------|
| Menu | Menu | Menu |
| Home | Home | Accueil |
| Shop All | Shop All | Tous les produits |
| New Arrivals | New Arrivals | Nouveautés |
| Best Sellers | Best Sellers | Meilleures ventes |
| Deals | Deals | Offres spéciales |
| Categories | Categories | Catégories |
| Search | Search products, brands... | Rechercher des produits, marques... |
| Cart | Cart | Panier |
| Special Offer | Special Offer | Offre spéciale |
| Need Help? | Need Help? | Besoin d'aide? |
| Support | 24/7 Support Center | Centre d'assistance 24/7 |

---

## Technical Details

### Files Modified

1. **`header.php`** - Added language and theme toggle buttons
2. **`functions.php`** - Added language/theme initialization and localization
3. **`assets/gamtech-unified.css`** - Added light mode CSS variables and toggle styles
4. **`assets/gamtech-unified.js`** - Added theme toggle and language switcher logic

### How It Works

#### Language System
- Uses browser cookies to store user's language preference
- Text translation is handled via JavaScript on page load
- Language strings are passed from PHP via `gtLang` object
- DOM elements are updated dynamically when language changes

#### Theme System
- Uses CSS custom properties (variables) for theming
- Light mode adds `gs-light-mode` class to `<html>` element
- CSS variables automatically update based on the class
- Persists using `localStorage` with key `gs-theme`

---

## Code Examples

### For Developers

#### Adding New Translations

Edit `wp-content/themes/woodmart-child/functions.php` in the `gamtech_init_language_theme_support()` function:

```php
'en' => array(
    'your_key' => 'Your English Text',
    // ...
),
'fr' => array(
    'your_key' => 'Votre texte français',
    // ...
),
```

#### Using Translations in JavaScript

Access translations via the global `gtLang` object:

```javascript
var englishText = gtLang.en.home;  // "Home"
var frenchText = gtLang.fr.home;   // "Accueil"
```

#### Detecting Current Language

```javascript
var currentLang = cookieGet('gt_lang') === 'fr' ? 'fr' : 'en';
```

#### Detecting Current Theme

```javascript
var isDarkMode = !document.documentElement.classList.contains('gs-light-mode');
```

---

## User Experience

### First Visit
- Site loads in **Dark mode** (default)
- Language defaults to **English** (default)
- Users see both toggle buttons in the header

### Subsequent Visits
- Theme preference is restored from `localStorage`
- Language preference is restored from cookie
- Site loads with previously selected theme and language

### Cookie & Storage Info
- **Language Cookie**: `gt_lang` (365 days expiration)
- **Theme Storage**: `gs-theme` (localStorage, session-based)
- Both are set with `SameSite=Lax` for security

---

## Browser Compatibility

The language and theme features use:
- **CSS Custom Properties** (CSS Variables) - Supported in all modern browsers
- **LocalStorage & Cookies** - Supported in all modern browsers
- **JavaScript ES5** - Compatible with older browsers

Tested on:
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

---

## Troubleshooting

### Theme Not Persisting
- Check if localStorage is enabled in browser settings
- Clear browser cache and try again
- Verify the `gs-theme` key is present in localStorage

### Language Not Changing
- Ensure cookies are enabled
- Check if `gt_lang` cookie is being set
- Clear cookies and try again

### Buttons Not Appearing
- Check browser console for JavaScript errors
- Verify CSS file is loaded (check Network tab)
- Ensure JavaScript is enabled

---

## Future Enhancements

Potential improvements:
1. Add more language support (Spanish, German, Arabic, etc.)
2. RTL language support for Arabic/Hebrew
3. Color scheme customization in WordPress admin
4. User preferences saved to database (for logged-in users)
5. Automatic language detection based on browser locale
6. Theme persistence across multiple devices (if user account exists)

---

## Support

For issues or questions about the language and theme features, contact the development team or visit the admin panel at `/wp-admin/`.
