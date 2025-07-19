# 📈 WooCommerce Product Price History & Chart

This WordPress snippet/plugin tracks WooCommerce product price changes and displays a historical price chart on the single product page using Chart.js.

For Farsi [Click Here](https://github.com/Hoseinkhalaj1/WooCommerce-Product-Price-History-Chart/blob/main/README-Fa.md)

---

## ✨ Features

- Automatically saves the product's price on each update
- Prevents saving duplicate price values
- Keeps only the latest 30 price records
- Chart display is device-sensitive (mobile, desktop, or all)
- Renders a beautiful animated chart using Chart.js 4
- Responsive chart layout with loading spinner

---

## ⚙️ How to Use

1. Copy the code into your theme’s `functions.php` file or a custom plugin.
2. Make sure WooCommerce is installed and activated.
3. Insert the following shortcode on the product page:

`[price_chart]`

### 🎛 Device Filter (Optional)

You can limit where the chart appears:

| `device` value | Description          |
|----------------|----------------------|
| `all`          | Show on all devices  |
| `mobile`       | Show only on mobile  |
| `desktop`      | Show only on desktop |

#### Examples:


---

## 🧠 How It Works

- Every time a product is saved, it checks the current price.
- If the price is different from the last stored price, a new entry with timestamp is saved.
- Only the latest 30 price records are kept to avoid data bloat.
- The chart will only be shown if there are at least two entries.

---

## 📊 Chart Details

- **Type:** Line chart
- **Library:** [Chart.js v4.4.4](https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js)
- **Tooltip:** Includes both date and formatted price (Vazirmatn font)
- **Loader:** Animated spinner shown before rendering

---

## 📌 Notes

- Works only on **single product pages**
- If there are fewer than two price records, a fallback message is displayed
- You can customize chart fonts, colors, and layout via CSS

---

## 🔮 Future Suggestions

- Display price chart in WooCommerce admin panel
- Add export button (CSV/Image)
- Add settings panel for appearance customization

---

## ✅ Requirements

- WordPress 5.0+
- WooCommerce 4.0+
- PHP 7.2+

---

## 📥 License

This project is open-source. You may freely use, edit, and include it in your personal or commercial projects.

---

Made with ❤️ for WooCommerce users.
