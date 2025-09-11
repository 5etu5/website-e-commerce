# Vintage Threads E-commerce Platform

## Overview

Vintage Threads is an e-commerce platform specializing in vintage clothing and fashion items. The application features a web-based storefront with product catalog, shopping cart functionality, and user account management. The platform emphasizes a vintage aesthetic with warm color schemes and classic typography to match the brand identity.

## User Preferences

Preferred communication style: Simple, everyday language.

## System Architecture

### Frontend Architecture
The frontend uses a traditional server-rendered web application approach with progressive enhancement through JavaScript. The styling is built with custom CSS that implements a vintage-themed design system using CSS custom properties for consistent color management. The interface leverages Bootstrap for responsive layout components while maintaining custom vintage styling overlays.

### Backend Architecture
The application uses a Node.js/TypeScript backend with a serverless database architecture. The database layer is implemented using Drizzle ORM with Neon's serverless PostgreSQL service, providing scalable database connectivity without traditional connection pooling overhead. The schema is shared between frontend and backend through a common module structure.

### Data Storage
The system uses Neon's serverless PostgreSQL database for persistent data storage. The database connection is managed through a connection pool that automatically handles scaling and connection management. The Drizzle ORM provides type-safe database operations and schema management.

### Client-Side Functionality
Interactive features are implemented with vanilla JavaScript using modern fetch APIs for AJAX requests. The shopping cart functionality includes real-time updates, loading states, and user feedback through notifications. The code follows progressive enhancement principles, ensuring basic functionality works without JavaScript.

## External Dependencies

- **Neon Database**: Serverless PostgreSQL hosting service for data persistence
- **Drizzle ORM**: TypeScript ORM for database operations and schema management
- **WebSocket (ws)**: Node.js WebSocket library for real-time database connections
- **Bootstrap**: CSS framework for responsive layout components (referenced in custom styles)
- **Font Awesome**: Icon library for user interface elements
- **Google Fonts**: Typography service for Georgia serif font family