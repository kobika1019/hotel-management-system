# Blue Moon Hotel Management System

**Project Name:** Hotel Management System  
**Group Number:** 11  
**Course:** IT2133 – Advanced Web Programming

---

## Group Members

- 2023/ICT/139 – R. Kobika
- 2023/ICT/79 – Ms. Deepawansha
- 2023/ICT/148 – Miss. Amarashinghe K.K.D.G
- 2023/ICT/99 – Ms. Reema F.F
- 2023/ICT/15 – Ms. Jayarathna W.A.D.P
- 2023/ICT/59 – Ms. Wimalasiri M.R.P.H.A

---

## About the Project

Blue Moon Hotel Management System is a web-based application developed to manage basic hotel activities easily and efficiently.

Customers can register, log in, view room details, and make room bookings through the website. Staff members can access the staff dashboard to view customer booking information. The admin can manage rooms and bookings through the admin dashboard.

This system helps reduce manual work and makes hotel management easier and more organized.

## Project Objectives

- To develop a user-friendly hotel management website.
- To allow customers to register and log in.
- To display room details and prices.
- To allow customers to book rooms online.
- To manage customer booking information.
- To allow customers to view their booking history.
- To help staff monitor guest booking activities.
- To allow the admin to manage rooms and bookings.

## Main Features

### Customer Features

- Register a new account.
- Log in and log out.
- View room details and prices.
- Book hotel rooms.
- View personal booking history.
- Request cancellation of pending bookings.

### Staff Features

- Staff login.
- Access the staff dashboard.
- View customer booking details.
- Monitor booking activities.

### Admin Features

- Admin login.
- View dashboard details.
- Add new rooms.
- Edit room information.
- Delete rooms.
- View room information and room types.
- View customer bookings.
- Approve or reject pending bookings.
- Manage booking statuses.

## Technologies Used

| Technology | Purpose |
|---|---|
| HTML | Creating web pages |
| CSS | Styling web pages |
| Bootstrap 5 | Responsive website design |
| JavaScript | Adding interactive features |
| PHP | Handling server-side functions |
| MariaDB / MySQL | Storing application data |
| XAMPP | Running the project locally |
| phpMyAdmin | Managing the database |
| Git & GitHub | Version control and teamwork |

## Database

The project uses a MariaDB / MySQL database named:

`blue_moon_hotel_db`

Main database tables:

- `users` – Stores customer, staff, and admin account details.
- `room_types` – Stores room categories and prices.
- `rooms` – Stores hotel room information.
- `bookings` – Stores customer booking details.

## Security Features

- Password hashing using PHP `password_hash()`.
- Password verification using PHP `password_verify()`.
- Prepared statements for database queries.
- Form validation.
- Session-based authentication.
- Role-based access control for Admin, Staff, and Customers.
- CSRF token protection for applicable forms.
- Booking date validation.
- Booking conflict checking to prevent overlapping reservations.
- Output escaping using `htmlspecialchars()`.

---

## Developed By Group 11

**University of Vavuniya**  
**Faculty of Applied Science**  
**Blue Moon Hotel Management System**