# tiscacatalin.com - Personal Website

Welcome to the repository of my personal website! This project showcases my portfolio, blog, and professional information. It is built using modern web technologies to ensure a fast, responsive, and maintainable experience.

## 🚀 Technologies Used

- **Frontend**: [Next.js](https://nextjs.org/) - A React framework for server-side rendering, static site generation, and API routes.
- **Styling**: [Tailwind CSS](https://tailwindcss.com/) - A utility-first CSS framework for rapid UI development.
- **Template**: [Tailwind UI - Spotlight Template](https://tailwindui.com/templates/spotlight) - A professionally designed template for portfolios and personal websites.
- **Backend**: [Strapi CMS](https://strapi.io/) - A headless CMS for managing content like blog posts, projects, and more.
- **Deployment**: Hosted on [Vercel](https://vercel.com/) for the frontend and [Render](https://render.com/) (or another provider) for the Strapi backend.

---

## 🌟 Features

- **Portfolio Showcase**: Display my projects, skills, and professional experience in a clean and modern layout.
- **Blog**: A dynamic blog section powered by Strapi CMS, allowing me to easily create and manage blog posts.
- **Responsive Design**: Built with Tailwind CSS, the website is fully responsive and works seamlessly on all devices.
- **SEO Optimization**: Leveraging Next.js's built-in SEO features, including meta tags and server-side rendering.
- **Fast Performance**: Static site generation and optimized assets ensure fast load times.

---

## 🛠 Implementation Details

### 1. **Frontend with Next.js**
The frontend is built using Next.js, which allows for:
- Static site generation (SSG) for fast page loads.
- Server-side rendering (SSR) for dynamic content.
- API routes for connecting to the Strapi backend.

### 2. **Tailwind CSS & Tailwind UI Template**
The design is based on the **Spotlight** template from Tailwind UI, which provides a sleek and professional layout. Tailwind CSS is used for styling, enabling rapid development and customization.

### 3. **Strapi CMS Integration**
Strapi serves as the backend for managing content:
- Blog posts, projects, and other dynamic content are stored in Strapi.
- The frontend fetches data from Strapi using GraphQL or REST API (depending on your implementation).
- Strapi's flexible content types make it easy to add or modify content without touching the codebase.

### 4. **Deployment**
- The Next.js frontend is deployed on **Vercel**, which provides seamless integration with GitHub and automatic deployments.
- The Strapi backend is hosted on **Render** (or another provider), ensuring reliable content management.

---

## 📂 Project Structure

```
tiscacatalin.com/
├── cms               # Strapi CMS
├── personal          # Next.js app
└── README.md         # This file
```

---

## 🌐 Live Demo

Check out the live website: [https://tiscacatalin.com](https://tiscacatalin.com)

---

## 📄 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---

## 🙏 Acknowledgments

- [Tailwind UI](https://tailwindui.com/) for the beautiful Spotlight template.
- [Strapi](https://strapi.io/) for the flexible and easy-to-use CMS.
- [Next.js](https://nextjs.org/) and [Vercel](https://vercel.com/) for the powerful frontend framework and hosting.
