# Domain Status Watcher
A Symfony Console application meant to monitor domain statuses

## How it works

It monitors domain statuses and notifies via Telegram when the domain is expired.

## Requirements

1. `docker` &mdash; [How to install `docker`](https://docs.docker.com/get-docker/)
2. `docker-compose` &mdash; [How to install `docker-compose`](https://docs.docker.com/compose/install/)
5. An internet connection

## Installation

1. Clone this repository: ``git clone https://github.com/lucian-vasile/domain-status-watcher.git``
2. Change directory: `cd domain-status-watcher`
3. Configure your '.env' file (eg: `cp .env.example .env`)
2. Install by running: `docker-compose -f install.yml up`
3. Wait for the installer to finish
4. Run `docker-compose up -d`

## Configuration

### Get notified via Telegram when a domain is unregistered

1. Make sure you have a Telegram account.
2. Add What's my ID bot to telegram: [t.me/my_id_bot](https://t.me/my_id_bot)
3. Get that value and replace `channel_id` in your .env
4. Add DomWatchBot to telegram: [t.me/DomWatchBot](https://t.me/DomWatchBot)

## Running

Add a new domain to monitor:

`docker-compose exec queue php /app/bin/console domains:add example.com`
