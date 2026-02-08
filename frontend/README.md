# React Frontend For Time Logger

## Standalone Installation:

> Note: This README addresses only the front-end app, in case you want to run it separately. If you want to run the entire
> application with the backend API, please refer to the main README in the root directory.

```bash
git clone https://github.com/ahmed-fawzy99/time-logger.git
cd time-logger/frontend
cp .env.example .env # Update your .env file if you need to expose different ports
```

> Note: The frontend assumes the backend is available at the URL defined by `VITE_API_URL` and `VITE_API_PORT` in the `.env` file. If you're using differrent ports, update the `.env` file accordingly.

### Using Docker:

```bash
docker compose up -d --build
```

### Non-Docker:

```bash
npm i

# for dev view
npm run dev

# for production build
npm run build

npm install -g serve
serve -s dist -l 3000
```
