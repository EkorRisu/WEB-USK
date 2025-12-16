# Coffee Shop Application

Aplikasi pemesanan kopi dengan fitur takeaway/dine-in, sistem topping, dan panel admin untuk manajemen menu.

## Features

### Customer Features
- ☕ Browse menu kopi, non-kopi, makanan ringan, dan dessert
- 🧊 Pilih toppings untuk minuman (Extra Shot, Whipped Cream, Vanilla Syrup, dll)
- 🥡 Pilih jenis pemesanan: Takeaway atau Dine-in
- 🪑 Untuk dine-in: pilih nomor meja
- 🛒 Keranjang belanja dengan kalkulasi otomatis
- 💳 Checkout dengan informasi pengiriman
- 📋 Riwayat transaksi dan status pemesanan
- ⭐ Sistem review produk
- 💝 Wishlist menu favorit
- 🌙 Dark mode toggle

### Admin Features
- 📊 Dashboard dengan statistik
- 🍽️ CRUD menu/produk dengan foto
- 🏷️ Manajemen kategori
- 🧊 CRUD toppings
- ✅ Konfirmasi pesanan (Ajax)
- 👥 Manajemen user
- 💬 Chat dengan customer
- 📈 Laporan transaksi

## Tech Stack

- **Backend**: Laravel 11
- **Frontend**: Blade Templates + Tailwind CSS + Alpine.js
- **Database**: MySQL
- **Icons**: Font Awesome
- **Notifications**: SweetAlert2
- **Development**: Laragon (Windows)

## Installation (Laragon Environment)

### Prerequisites
- Laragon (with PHP 8.2+, MySQL, Composer)
- Node.js & NPM

### Setup Steps

1. **Clone Repository**
   ```bash
   cd C:\laragon\www
   git clone <repository-url> coffe-shop
   cd coffe-shop
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Configuration**
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```

4. **Database Configuration**
   Edit `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=coffe_shop
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Create Database**
   - Open HeidiSQL atau phpMyAdmin
   - Create database: `coffe_shop`

6. **Run Migrations & Seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Storage Link**
   ```bash
   php artisan storage:link
   ```

8. **Build Assets**
   ```bash
   npm run build
   # or for development
   npm run dev
   ```

9. **Start Server**
   - Laragon will automatically serve at: `http://coffe-shop.test`
   - Or manually: `php artisan serve`

## Default Users

### Admin Account
- **Email**: admin@coffeeshop.com
- **Password**: admin123
- **Access**: Full admin panel

### Customer Account
- **Email**: customer@coffeeshop.com
- **Password**: customer123
- **Access**: Customer features

## Sample Data

Seeder akan menambahkan:
- **4 Kategori**: Coffee, Non-Coffee, Makanan Ringan, Dessert
- **16 Produk**: Espresso, Americano, Cappuccino, Latte, dll
- **8 Toppings**: Extra Shot, Whipped Cream, Vanilla Syrup, dll

## Usage Guide

### For Customers
1. Register/Login ke sistem
2. Browse menu berdasarkan kategori
3. Klik produk untuk melihat detail dan pilih topping
4. Pilih jenis pesanan (Takeaway/Dine-in)
5. Tambah ke keranjang dan checkout
6. Tunggu konfirmasi admin
7. Berikan review setelah pesanan selesai

### For Admin
1. Login dengan akun admin
2. Akses dashboard untuk melihat statistik
3. Kelola menu di "Menu Management"
4. Kelola kategori di "Kategori Menu"
5. Kelola toppings di "Topping Management"
6. Konfirmasi pesanan di "Transactions"
7. Chat dengan customer di "Messages"

## File Structure

```
app/
├── Http/Controllers/
│   ├── Admin/           # Admin controllers
│   └── User/            # User controllers
├── Models/              # Eloquent models
└── Services/            # Service classes

resources/
├── views/
│   ├── admin/           # Admin views
│   ├── user/            # User views
│   ├── auth/            # Authentication views
│   └── layouts/         # Layout templates
└── css/                 # Stylesheets

database/
├── migrations/          # Database migrations
└── seeders/            # Database seeders
```

## API Endpoints

### Public Routes
- `GET /` - Homepage
- `GET /login` - Login page
- `GET /register` - Register page

### User Routes (Auth Required)
- `GET /dashboard` - User dashboard
- `GET /cart` - Shopping cart
- `POST /cart/add` - Add to cart
- `GET /checkout` - Checkout page
- `POST /checkout` - Process order

### Admin Routes (Admin Only)
- `GET /admin/dashboard` - Admin dashboard
- `GET /admin/produk` - Product management
- `GET /admin/kategori` - Category management
- `GET /admin/topping` - Topping management
- `GET /admin/transactions` - Transaction management

## Troubleshooting

### Common Issues

1. **Migration Error**
   ```bash
   php artisan migrate:reset
   php artisan migrate
   php artisan db:seed
   ```

2. **Permission Issues**
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

3. **Asset Not Loading**
   ```bash
   npm run build
   php artisan storage:link
   ```

4. **Database Connection**
   - Check MySQL service in Laragon
   - Verify database name and credentials in `.env`

## License

This project is licensed under the MIT License.