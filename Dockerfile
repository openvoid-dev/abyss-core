# Use the official PHP image with CLI
FROM php:8.1-cli

# Set the working directory inside the container
WORKDIR /home/antonio/code/abyss-core

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && docker-php-ext-install pdo pdo_mysql

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the application code to the container (for production use)
COPY . /home/antonio/code/abyss-core

# Run Composer to install dependencies
RUN composer install

# Install and make Tailwind CSS executable
RUN curl -sLO https://github.com/tailwindlabs/tailwindcss/releases/latest/download/tailwindcss-linux-x64 \
    && mv tailwindcss-linux-x64 tailwindcss && chmod +x tailwindcss

# Expose port 8383 to allow external connections
EXPOSE 8383

# Run both Tailwind CLI and PHP server
CMD ["sh", "-c", "php whisper serve --host=0.0.0.0 --port=8383"]
