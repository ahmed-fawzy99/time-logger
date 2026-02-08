# Build stage for production React/Vite app
FROM node:24-alpine AS builder

# Install pnpm
RUN corepack enable && corepack prepare pnpm@latest --activate

# Set working directory
WORKDIR /app

# Copy package files
COPY package.json ./

# Install dependencies
RUN pnpm install

# Copy source code
COPY . .

# Accept build arguments for Vite environment variables
ARG VITE_API_URL
ARG VITE_API_PORT
ARG VITE_API_VERSION

# Set environment variables for build
ENV VITE_API_URL=${VITE_API_URL}
ENV VITE_API_PORT=${VITE_API_PORT}
ENV VITE_API_VERSION=${VITE_API_VERSION}

# Build the application
RUN pnpm build

# Production stage - lightweight static file server
FROM node:24-alpine AS production

WORKDIR /app

# Install a lightweight static file server
RUN npm install -g serve

# Copy built assets from builder stage
COPY --from=builder /app/dist ./dist

# Expose port
EXPOSE 3000

# Serve the static files with SPA support
CMD ["serve", "-s", "dist", "-l", "3000"]
