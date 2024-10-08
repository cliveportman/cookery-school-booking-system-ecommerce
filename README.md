# Cookery school booking system with ecommerce
*FORMER CLIENT PROJECT - when I joined my current employer, I handed over this client to another team.*

A client I worked with for many years, this is an ecommerce site and a booking system in one project all built using Craft CMS and Craft Commerce. The booking system was bespoke and was used by the front desk for signing-in attendees and the back office for managing bookings. The ecommerce side of the site was used for selling cookery courses, primarily to US tourists visiting Paris for an in-person experience.

## What does it show?
- Complete solution design to solve a client's problem, working directly with stakeholders
- Full-stack web development (HTML, CSS, Javascript, PHP)
- User-interface design
- Integration with third-party APIs

## What tech was used?
![craft cms](https://img.shields.io/badge/Craft_CMS-brown)
![craft commerce](https://img.shields.io/badge/Craft_Commerce-brown)
![semantic ui](https://img.shields.io/badge/Semantic_UI-brown)
![html](https://img.shields.io/badge/HTML-blue)
![javascript](https://img.shields.io/badge/Javascript-blue)
![css](https://img.shields.io/badge/CSS-blue)
![PHP](https://img.shields.io/badge/PHP-blue)

### Frontend
The public-facing ecommerce site was build using Twig templates, rendered server-side by Craft CMS. There was minor use of JQuery with a few plugins like Masonry, and Bootstrap for the grid system. The site was fully responsive and mobile-first, designed to a spec provided by https://specialdesignstudio.com/.
The booking system was built using Semantic UI.

### Backend
Craft CMS and Craft Commerce, originally hosted on a Linode server running CPanel before migrating to a Linode server managed via Laravel Forge.
Payment processing required a custom plugin using a French bank's proprietary payment gateway, various third-party plugins were used, as were several plugins developed by myself to cover missing functionality.
Substantial plugin work was required for the booking system, including a product calendar and automatic stock replenishment, again to make up for missing features in the ecosystem.

## Who was the client?
A cookery school primarily aimed at US tourists visiting Paris. They client sought us out after seeing previous work we'd done for a major cookery school in the UK, wanting to work a small team with experience of scale, developing booking systems and ecommerce.

## Why?
 Despite the business doing well, the client's existing booking system was making the day-to-day running of the business very difficult. They felt they were missing out on sales opportunities through poor search engine positioning and even worse user experience when it came to their ecommerve site.

## Technical challenges
Developing the payment gateway for the French bank. Craft Commerce had no integration for them, and the development process wasn't exactly like you get from providers like Stripe or PayPal. The bank's API was poorly documented and required a lot of back-and-forth to get working.

The biggest challenge was the booking system, which was a bespoke build. Just designing the requirements was a project in itself, then the development required a lot of bespoke plugin work to get it working as required. After several years, we started experiencing performance issues, so a lot of work was done to improve caching and reduce the number of database queries. Building the same thing today, we'd use a headless CMS with a JS framework like React (more likely Svelte), but at the time, this was the best solution.

## Screenshot
![Screenshot of the cookery school's homepage'](screenshot.png)