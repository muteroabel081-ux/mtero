# 🛍️ GIGI STORES — Premium Footwear & Accessories

A elegant, single-page e-commerce storefront for **Gigi Stores**, a Nairobi-based retailer of handcrafted footwear, handbags, jewelry, sunglasses, and summer hats. Built with pure HTML, Tailwind CSS, and vanilla JavaScript — no frameworks, no build tools.

---

## ✨ Features

- **Product Catalogue** — 70+ products across categories: Shoes, Big Handbags, Jewelry, Sunglasses, and Summer Hats
- **Category Filter Chips** — Dynamically generated filters to browse by category
- **Shopping Cart Sidebar** — Slide-in cart with item quantity controls and live order totals
- **Smart Delivery Pricing** — Free delivery on orders over KES 3,000; otherwise KES 250
- **Order Review & Checkout** — Customers review their full order before confirming payment
- **M-Pesa STK Push Simulation** — Simulated mobile money payment flow
- **Gift Note Support** — Optional handwritten gift message with orders
- **Persistent Cart** — Cart state saved in `localStorage` across page refreshes
- **Toast Notifications** — Animated feedback when items are added to the cart
- **Responsive Design** — Fully mobile-friendly layout
- **WhatsApp & Call CTAs** — Direct contact buttons in the footer

---

## 🗂️ Project Structure

```
gigi-stores/
├── index.html          # Main storefront (all HTML, CSS, and JS in one file)
└── images/             # Product image directory
    ├── AEBM5567.JPG
    ├── ALPQ7041.JPG
    └── ... (all product images)
```

> All product images should be placed in the `images/` folder. Image filenames must match those defined in the `productEntries` array inside `index.html`.

---

## 🚀 Getting Started

No installation or build step required.

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/gigi-stores.git
   cd gigi-stores
   ```

2. **Add product images**
   Place all `.JPG` product images into the `images/` folder.

3. **Open in browser**
   Simply open `index.html` in any modern web browser:
   ```bash
   open index.html
   ```
   Or serve it locally with any static file server:
   ```bash
   npx serve .
   ```

---

## 🛒 How the Cart Works

- Clicking **"Add to Bag"** adds the product to the cart and opens the cart sidebar.
- Quantities can be increased or decreased using the `+` / `-` buttons.
- Customers fill in their **Name**, **Contact Number**, **Delivery Address**, and **M-Pesa Number** before checking out.
- An order review confirmation dialog is shown before the payment is triggered.
- On confirmation, an STK push simulation is initiated (replace with real M-Pesa Daraja API integration for production).

---

## 💳 Payment Integration (Production Note)

The current checkout simulates an M-Pesa STK Push. To go live, integrate with the **Safaricom Daraja API**:

- Register at [developer.safaricom.co.ke](https://developer.safaricom.co.ke)
- Implement the STK Push endpoint (`/mpesa/stkpush/v1/processrequest`)
- Replace the `setTimeout` simulation in `proceedToPayment()` with a real API call

---

## 🎨 Tech Stack

| Technology | Purpose |
|---|---|
| HTML5 | Structure |
| [Tailwind CSS](https://tailwindcss.com) (CDN) | Styling & layout |
| [Animate.css](https://animate.style) | Entry animations & toasts |
| [Google Fonts](https://fonts.google.com) — Playfair Display + Inter | Typography |
| Vanilla JavaScript | Cart logic, filtering, UI interactions |
| `localStorage` | Cart persistence across sessions |

---

## 📦 Adding or Editing Products

Products are defined in the `productEntries` array in `index.html`:

```js
{ img: "FILENAME.JPG", name: "Product Name", category: "Shoes", price: 6800 }
```

- **`img`** — filename of the image inside the `images/` folder
- **`name`** — display name on the product card
- **`category`** — used for filter chips; must match a key in `categoryPrice` for default pricing
- **`price`** *(optional)* — if omitted, the category default price is used

**Default prices by category:**

| Category | Default Price (KES) |
|---|---|
| Shoes | 5,500 |
| Big Handbags | 5,500 |
| Jewelry | 3,000 |
| Sunglasses | 2,500 |
| Summer Hats | 1,200 |

---

## 📍 Contact

**GIGI STORES**
📍 Nairobi, Kenya
📞 +254 712 652 655
💬 [WhatsApp](https://wa.me/254712652655)

---

## 📄 License

This project is proprietary. All rights reserved © Gigi Stores.
