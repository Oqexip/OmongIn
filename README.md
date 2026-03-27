# OmongIn (Anonboard)

![Laravel](https://img.shields.io/badge/Laravel-12-red?logo=laravel)  
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3-blue?logo=tailwindcss)  
![License](https://img.shields.io/badge/license-MIT-green)  

**OmongIn** is a professional anonymous discussion platform built with **Laravel 12** and **TailwindCSS**. It provides a secure and intuitive environment for users to create threads, post comments, and engage in discussions without disclosing their personal identity.

---

## Features

### Core Functionality
- **Authentication:** Integrated registration, login, and comprehensive profile management.
- **Board & Thread Management:** Support for creating custom boards and initiating focused discussions.
- **Nested Commenting Systems:** Threaded views for replies to ensure clear and organized conversations.
- **Anonymous Session Tracking:** Advanced middleware allows non-registered users to interact with the platform while maintaining session integrity.
- **Content Persistence:** Users can modify or remove their own posts and comments within a defined time limit.

### Interaction & Moderation
- **Voting System:** Robust upvote and downvote mechanism for both threads and comments.
- **Trending Metrics:** A dedicated popular threads page highlighting high-engagement discussions.
- **Content Reporting:** Integrated reporting system for moderating spam, abuse, and sensitive content.
- **Advanced Discovery:** Global search functionality combined with granular filtering options (Newest, Top, Hot).
- **Sensitive Content Management:** Built-in protection for spoilers and NSFW tags with click-to-reveal functionality.

### User Experience
- **Modern Aesthetic:** A clean, monochrome design language that supports both dark and light modes.
- **Responsive Architecture:** Fully optimized for various screen sizes using TailwindCSS and the Inter type system.
- **Media Support:** Capability to attach images with automatic blurring for sensitive content.

---

## Technical Stack
- **Framework:** Laravel 12 (PHP)
- **Database:** MySQL
- **Styles:** TailwindCSS
- **Interactivity:** Alpine.js

---

## Installation

Follow these steps to set up the development environment:

1. **Clone the repository**
   ```bash
   git clone https://github.com/Oqexip/Anonboard.git
   cd Anonboard
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Initialize database**
   ```bash
   php artisan migrate --seed
   ```

5. **Start server**
   ```bash
   php artisan serve
   ```

---

## Contribution

We welcome contributions to the project. Please follow the standard GitHub workflow:

1. Fork the repository.
2. Create a dedicated feature branch (`git checkout -b feature-name`).
3. Commit your changes with descriptive messages (`git commit -m "Brief description of changes"`).
4. Push to your branch (`git push origin feature-name`).
5. Open a Pull Request for review.

---

## License

This project is licensed under the [MIT License](LICENSE).

---

Developed by Oqexip as an open-source platform for learning and collaboration.
