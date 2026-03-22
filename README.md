# OmongIn 📝  

![Laravel](https://img.shields.io/badge/Laravel-12-red?logo=laravel)  
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3-blue?logo=tailwindcss)  
![License](https://img.shields.io/badge/license-MIT-green)  

**OmongIn** is a simple anonymous discussion platform built with **Laravel 12 + TailwindCSS**.  
Users can create threads, post comments, and reply without revealing their real identity.  

---

## ✨ Features

**Core Features**
- 🔐 **Authentication:** Register, login, and profile management (`/profile`).
- 🗂️ **Boards & Threads:** Create custom boards and start discussions.
- 💬 **Nested Comments:** Reply to threads and other comments with threaded views.
- 🎭 **Anonymous Sessions:** Middleware tracking allows anonymous users to post and vote without an account.
- ✏️ **Edit & Delete:** Modify or remove your own posts/comments (editing is limited to 15 minutes).

**Interaction & Moderation**
- ⬆️ **Voting System:** Upvote and downvote threads or comments.
- 📈 **Popular Threads:** Trending page (`/popular`) showcasing the most interactive discussions.
- 🚩 **Reporting System:** Report problematic content (Spam, Abuse, NSFW, etc.).
- 🔍 **Search & Filters:** Global search functionality and advanced sorting (Newest, Oldest, Top, Hot).
- 🙈 **Spoilers & NSFW Tags:** "Click to reveal" protection for sensitive text and images.

**UI/UX**
- 🌗 **Monochrome Design:** Clean, modern monochrome aesthetic with **Dark Mode** & **Light Mode** toggle.
- 📱 **Responsive:** Fully responsive design using TailwindCSS, featuring an Inter font typography.
- 📎 **Media Uploads:** Support for image attachments with spoiler/NSFW blurring capabilities.

---

## 🛠️ Tech Stack
- [Laravel 12](https://laravel.com/) (PHP Framework)  
- [MySQL](https://www.mysql.com/) (Database)  
- [TailwindCSS](https://tailwindcss.com/) (UI Styling)  
- [Alpine.js](https://alpinejs.dev/) (Lightweight Interactivity)  

---

## ⚙️ Installation
1. Clone the repository
   ```bash
   git clone https://github.com/Oqexip/Anonboard.git
   cd Anonboard
````

2. Install dependencies

   ```bash
   composer install
   npm install && npm run build
   ```

3. Create `.env` file and configure your database

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Run migrations & seeders

   ```bash
   php artisan migrate --seed
   ```

5. Start the development server

   ```bash
   php artisan serve
   ```
---

## 🤝 Contribution

Contributions are welcome!

1. Fork this repository
2. Create a new branch (`git checkout -b feature-name`)
3. Commit your changes (`git commit -m "Add new feature"`)
4. Push to your branch (`git push origin feature-name`)
5. Open a Pull Request

---

## 📄 License

This project is licensed under the [MIT License](LICENSE).

---

💡 Built by Oqexip as a learning & sharing project.
