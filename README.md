# 📰 **PHP News Website (ConnectBihar Portal)**

A feature-rich, high-performance PHP & MySQL Content Management System (CMS) designed for modern news portals, online magazines, and publishing platforms. Features an intuitive front-end interface, multi-role admin dashboard, real-time analytics, and an automated web installation wizard.

---

## ✨ **Key Features**

### 🌐 **User-Facing Web Portal**
- **Real-Time News Feed**: Category-wise news display with instant updates and dynamic loading.
- **Categorized Navigation**: Organize news into custom categories (Results, Latest Updates, Admit Cards, News Articles, etc.).
- **Author Filtering**: Filter articles by specific journalists, authors, or anchors.
- **Instant Search**: Quick and accurate search capabilities across article titles, details, and content.
- **Social Media Integration**: Built-in dynamic sharing links for WhatsApp, Facebook, Instagram, and LinkedIn.
- **Visitor Analytics**: Live visitor count and IP tracking system.
- **Fully Responsive**: Optimized for seamless viewing across smartphones, tablets, and desktop displays.

### 🛡️ **Powerful Admin Dashboard**
- **Multilogin & Multi-Role Support**: Secure access control supporting Administrators, Authors, News Anchors, and Operators.
- **Category Management**: Create, update, toggle status (Active/Pending), and manage news categories.
- **Article & Content Manager**: Rich article editor to compose, edit, approve, reject, or archive news posts.
- **User & Staff Management**: Manage user roles, passwords, status (Active/Inactive), and profile details.
- **Async Notification Queue**: Lightning-fast background processor ensures the admin panel never slows down, queuing Telegram and Email notifications instantly.
- **Automated Author Emails**: Authors receive beautifully formatted HTML emails whenever their posts are approved, rejected, drafted, or published.
- **Website Settings & SEO**: Manage website name, base URL, logo, favicon, watermark image, SEO keywords, and contact details.
- **Visitor Traffic Insights**: Interactive dashboard displaying visitor logs and traffic metrics.

---

## 📋 **System Requirements**

Before installing, ensure your web server environment satisfies the following requirements:

| Requirement | Supported Version / Detail |
| :--- | :--- |
| **PHP Version** | `PHP 7.4` or higher (`PHP 8.0`, `8.1`, `8.2` fully supported) |
| **Database** | `MySQL 5.7+` or `MariaDB 10.3+` |
| **Web Server** | `Apache` or `Nginx` (XAMPP / WAMP / LAMP / MAMP compatible) |
| **PHP Extensions** | `mysqli`, `gd`, `session`, `file_uploads` enabled |

---

## 🚀 **Installation Guide**

You can install the PHP News Website using either the **Automated Web Wizard** (Recommended) or via **Manual Setup**.

---

### **Method 1: Automated Web Installer (Recommended)**

1. **Clone / Copy the Project**:
   Place the project directory into your web server's root folder (e.g., `C:\xampp\htdocs\PHP-News-Website`).

2. **Start Web Server & Database**:
   Launch your XAMPP / WAMP control panel and start both **Apache** and **MySQL**.

3. **Launch the Installer**:
   Open your browser and navigate to:
   ```text
   http://localhost/PHP-News-Website/install/
   ```

4. **Follow the 6-Step Installation Wizard**:
   - **Step 1 (Welcome)**: Review system capabilities and start installation.
   - **Step 2 (Requirements)**: Verify that PHP version, extensions, and file write permissions meet requirements.
   - **Step 3 (Database Setup)**: Enter MySQL host (`localhost`), username (`root`), password, and database name (`news_website`). The installer will automatically create the database and import `install/database.sql`.
   - **Step 4 (Site Configuration)**: Set your Site URL (e.g., `http://localhost/PHP-News-Website`) and Site Name.
   - **Step 5 (Admin Account)**: Create your administrator username and password (or keep default credentials).
   - **Step 6 (Finish)**: Setup complete! The wizard automatically locks itself by creating `install/install.lock`.

---

### **Method 2: Manual Installation**

If you prefer to configure the database manually:

1. **Create Database**:
   Open phpMyAdmin (`http://localhost/phpmyadmin/`) and create a new database named `news_website` with utf8mb4 encoding:
   ```sql
   CREATE DATABASE `news_website` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. **Import SQL File**:
   Import the file located at `install/database.sql` into your `news_website` database.

3. **Configure Connection Files**:
   - Open `config.php` and update the database settings:
     ```php
     $hostname = 'localhost';
     $username = 'root';
     $password = '';
     $dbname   = 'news_website';
     ```
   - Open `admin/app/config.php` and update the matching credentials:
     ```php
     $hostname = 'localhost';
     $username = 'root';
     $password = '';
     $dbname   = 'news_website';
     ```

4. **Update Website Settings in Database**:
   In phpMyAdmin, run the following query to set your local site URL:
   ```sql
   UPDATE settings SET websiteUrl = 'http://localhost/PHP-News-Website' WHERE 1;
   ```

5. **Lock Installer**:
   Create an empty file named `install.lock` inside the `install/` directory to protect the installation wizard.

---

## 📖 **Usage Guide**

### 🔗 **Access URLs**
- **Public News Website**: `http://localhost/PHP-News-Website/`
- **Admin Control Panel**: `http://localhost/PHP-News-Website/admin/`

---

### 🔑 **Default Admin Credentials**

| Role | Default Username | Default Password |
| :--- | :--- | :--- |
| **Administrator** | `Technicalbaba` | `admin` *(or password set during installer Step 5)* |
| **Website Owner** | `websiteowner` | `admin` |

*(Note: Passwords can be updated anytime from the Admin Panel under **Users** or **Settings**).*

---

### 🛠️ **Administrative Tasks**

1. **Publishing a News Article**:
   - Log in to the Admin Dashboard at `/admin`.
   - Navigate to **News Management** -> **Add New Post**.
   - Fill in Title, Short Summary, Description, Select Category, and Upload a Featured Image.
   - Click **Publish**.

2. **Managing News Categories**:
   - Go to **Category Management** -> **Add Category**.
   - Enter Category Name and Category Title.
   - Toggle status to `Active` (Y) to make it visible on the front-end menu.

3. **Updating Website Logo & Watermark**:
   - Go to **Manage Website** / **Settings**.
   - Upload your custom logo, favicon, and watermark images.
   - Save changes to apply site-wide.

4. **Managing Users & Permissions**:
   - Go to **User Management** -> **Add User**.
   - Assign roles:
     - `1`: Super Administrator
     - `2`: Author / Journalist
     - `3`: Editor / News Anchor

---

## 🤖 **Telegram Bot & Webhook Setup**

To enable automatic notifications and manage your website directly from Telegram, follow these steps to configure your bot and webhook.

### **1. Create a Telegram Bot**
- Open Telegram and search for **[@BotFather](https://t.me/BotFather)**.
- Send `/newbot` and follow the prompts to create your bot.
- Copy the **Bot Token** provided (e.g., `123456789:ABCDEF...`).

### **2. Configure Bot in System Manager**
- Go to the Admin Dashboard and open **Manage Website** -> **Settings** -> **Telegram Services** tab.
- Enable **Telegram Notifications** and paste your **Bot Token**.
- Add your bot to your Group and/or Channel as an **Administrator**.
- Click the **Extract IDs** button (Make sure the webhook is removed first!) to find and copy your Group ID or Channel ID, then paste them into the respective fields.
- Click **Save Telegram Settings**.

### **3. Setup Webhook (For Real-Time Updates)**
- *Note: You must have a live server with SSL (`https://`) to use Webhooks.*
- In the Telegram Services tab, scroll to the **Webhook Runner** section.
- Enter your webhook URL pointing to `admin/app/webhook.php` (e.g., `https://yourdomain.com/admin/app/webhook.php`).
- Click **Set Webhook**.
- Use the **Live Connection Test** card to verify that your bot is sending messages correctly!

### **4. Custom Templates & Localhost Image Uploads**
- **Smart Formatting**: Posts sent to a **Telegram Group** automatically include the **Author's Name** (great for internal teams). Posts sent to a **Telegram Channel** use a clean, author-less format suited for public audiences.
- **Localhost Support**: The bot is programmed to map local URLs (`http://localhost/...`) to physical absolute paths. This means rich image cards and photos will successfully upload to Telegram even when developing on `localhost` or a private network!

---

## ⏱️ **Cron Job Setup (Automated Tasks)**

The platform supports automated background tasks (like sending delayed notifications, clearing old logs, or cleaning up drafts) using Cron Jobs.

### **1. Configure on Live Server (cPanel / Plesk)**
- Open your hosting control panel (e.g., cPanel).
- Navigate to the **Cron Jobs** section.
- You need to set up two cron jobs for optimal performance:
  
  **A. Background Queue Processor (Runs Every 1 Minute)**
  *Processes all instant Telegram logs and Author Emails in the background without slowing down the website.*
  ```bash
  wget -q -O - https://yourdomain.com/cron/process-telegram-queue.php >/dev/null 2>&1
  ```

  **B. Scheduled Post Notifications (Runs Every 5 or 15 Minutes)**
  *Scans for newly published posts and sends bulk notifications to subscribers.*
  ```bash
  wget -q -O - https://yourdomain.com/cron/post-notifications.php >/dev/null 2>&1
  ```
  *(Alternatively, run the PHP script directly if your host allows: `/usr/local/bin/php /path/to/public_html/cron/process-telegram-queue.php`)*

### **2. Local Testing (XAMPP / WAMP)**
If you are developing locally and want to test the Cron Job without a live server:
- Open your web browser.
- Navigate directly to the cron script URLs:
  ```text
  http://localhost/PHP-News-Website/cron/process-telegram-queue.php
  http://localhost/PHP-News-Website/cron/post-notifications.php
  ```
- Alternatively, you can run it via the command line or PowerShell:
  ```powershell
  php.exe C:\xampp\htdocs\PHP-News-Website\cron\process-telegram-queue.php
  ```
- The script will execute once, allowing you to test if pending notifications or scheduled tasks run correctly.

---

## 📁 **Project File Structure**

```text
PHP-News-Website/
├── admin/                     # Administrative Dashboard
│   ├── app/                   # Backend logic & DB configuration
│   │   ├── config.php         # Admin DB Connection configuration
│   │   ├── _DBconnect.php     # Helper include for AJAX endpoints
│   │   ├── telegram_bot.php   # Telegram Bot API & Notification Helpers
│   │   └── app.php            # Form submit handlers & login logic
│   ├── dashboard.php          # Main dashboard stats & overview
│   ├── index.php              # Admin login page
│   ├── new-post.php           # Add new post interface
│   ├── view-post.php          # Datatable view of all posts
│   └── setting.php            # Site configuration settings
├── assets/                    # Static assets (CSS, JS, Images, Uploads)
├── cron/                      # Scheduled Tasks & Background Processes
│   ├── process-telegram-queue.php # Async queue processor for Emails & Telegram
│   └── post-notifications.php     # Bulk notifications scheduler
├── database/                  # Database helper functions
│   ├── Mailer.php             # Email SMTP/Native Mailer class
│   └── functions.php          # Shared utility functions & redirect helpers
├── install/                   # Web Installation Wizard
│   ├── database.sql           # Database schema & initial dump data
│   ├── index.php              # Installation wizard controller
│   └── install.lock           # Installation lock file (created post-setup)
├── 404.php                    # Custom error 404 page
├── category.php               # Category-wise news view
├── config.php                 # Root database & site configuration
├── header.php                 # Front-end header & navigation bar
├── footer.php                 # Front-end footer
├── index.php                  # Main home page news feed
├── search.php                 # Article search page
├── single.php                 # Detailed article view page
└── README.md                  # Project documentation
```

---

## 🔧 **Troubleshooting & FAQs**

### **Q: Redirected to `404.php` when opening home page?**
> **Fix**: Ensure your MySQL server is running, database `news_website` exists, and `websiteUrl` in table `settings` matches your local URL (e.g., `http://localhost/PHP-News-Website`).

### **Q: How do I re-run the Installation Wizard?**
> **Fix**: Delete the lock file located at `install/install.lock`, then re-open `http://localhost/PHP-News-Website/install/` in your browser.

### **Q: AJAX requests (like verifying usernames or loading posts) fail in Admin panel?**
> **Fix**: Ensure `admin/app/_DBconnect.php` exists and correctly includes `config.php`.

---

## 👨‍💻 **Author & Credits**

Developed with ❤️ by **Aman Projects / Technical Baba**.
- **Website**: [Aman Projects](https://amanprojects.com)
- **Technical Support**: [Technical Baba](https://technicalaman.co.in)

---

*Enjoy publishing with PHP News Website!* 🚀
