# TIME LOGGER

Self-hosted app for logging your work hours and generating invoices based on those hours. Built with React and Laravel, and designed to be simple, intuitive, and efficient for freelancers and small businesses.

# Features

- Support for multiple projects, time frames, and entries with descriptions
- Stopwatch feature for quick time logging.
- Configurable hourly rate, currency, and duration rounding
- Generate PDF invoices on time frame completion, with customizable title, name, address, and color
- Ability to control which time entries are billable and included in the invoice
- Support for PWA and offline usage, with data synchronization when back online

Tech Stack:

- Frontend: React, TypeScript, Tailwind CSS, Vite, TanStack Router, SWR.
- Backend: Laravel, PostgreSQL, Redis,
- Operation: Docker Compose for both frontend and backend, with separate configurations.

# Installation

Clone the repository:

```bash
git clone https://github.com/ahmed-fawzy99/time-logger.git
cd time-logger

cp backend/.env.example backend/.env
```

### Proceed with Docker Installation

The following steps assume you have Docker installed on your machine. If you don't have Docker, please refer to the [Docker installation guide](https://docs.docker.com/get-docker/) for your operating system.

The instructions below are for UNIX-based systems (Linux/macOS). If you're using Windows, you may need to adjust the commands accordingly.

```bash
docker compose -f backend/compose.yaml up -d --build && \
docker compose -f frontend/compose.yaml up -d --build
```

First time configurations (It may take a few seconds for the services to start up after running the above command.):

```bash
docker compose -f backend/compose.yaml exec -it app php artisan key:generate
docker compose -f backend/compose.yaml exec -it app php artisan db:seed --force
```

If you want to fill the database with records for testing, use this command instead:

```bash
# This will create sample projects, time frames, and time entries for you to test the project out.
docker compose -f backend/compose.yaml exec -it app php artisan db:seed --class=Database\\Seeders\\TestSeeder --force

# To undo and start fresh:
docker compose -f backend/compose.yaml exec -it app php artisan migrate:fresh --seed --force
```

Access the application by navigating to http://localhost:3999 in your web browser. You should see the Time Logger application running.

### Proceed with Non-Docker Installation

Please refer to the [frontend installation guide](frontend/README.md) and [backend installation guide](backend/README.md) for instructions on how to set up the frontend and backend separately.

# Project Structure and Concepts

Projects are the main entities in the application. Each project can have multiple time frames, and each time frame can have multiple time entries.

Time frames represent a period of work on a project. You can have multiple time frames for the same project, and they can be active at the same time. Time frames can be marked as "done" when you finish working on that period, which allows you to generate an invoice for the billable time entries within that time frame.

Time entries represent individual work sessions. Each time entry has a start time, an end time, and a duration. Time entries can be marked as "billable" or "non-billable". Only billable time entries will be included in the invoice when you generate it.

You can log your entries manually by filling out the form, or you can use the stopwatch feature to quickly log time. You can startuse the stopwatch, and once your done stop it and Choose "Convert to Time Entry" to quickly create a time entry with the duration set to the time elapsed on the stopwatch. The Stopwatch feature does not automatically create time entries for you unless you choose to convert the stopwatch time into a time entry.

Invoices are generated based on time frames. When a time frame is marked as "done", you get the option to generate an invoice.

- Only time entries that are marked as `billable` (default option) will be included in the invoice.
- The invoice will include the total billable hours, the hourly rate, and the total amount due.
- Even when a time frame is marked as "done", you can still edit it and add more time entries to it and generate an updated invoice.

Preferences page allows you to set your hourly rate, currency, and any invoice preferences. These preferences will be used when generating invoices and calculating amounts.

## Sample Usage

1. Create a new project called "Project A".
2. Define a new time frame for "Project A", called "Sprint 1", for example, and start the stopwatch.
3. Log your daily work sessions on that sprint, either by using the stopwatch or by defining start and end times of the period, and add an optional description of the work done to appear on the invoice.
4. Once you finish working on that sprint, mark the time frame as "done" and generate an invoice for it.
5. you can download it and send it to your client.

# Video Demonstration

Watch the video demonstration of the Time Logger application in action:

[![Time Logger Demo](https://img.youtube.com/vi/g6bwqajpCQs/0.jpg)](https://youtu.be/g6bwqajpCQs)

# Screenshots

<details>
  <summary>Screenshots</summary>

![Projects](https://pub-2d4798b44c2c47d1adecc6c62bf47f38.r2.dev/git/timer-logger/projects.png)
![Time Frames](https://pub-2d4798b44c2c47d1adecc6c62bf47f38.r2.dev/git/timer-logger/time-frames.png)
![Time Frame](https://pub-2d4798b44c2c47d1adecc6c62bf47f38.r2.dev/git/timer-logger/time-frame.png)
![Stopwatch](https://pub-2d4798b44c2c47d1adecc6c62bf47f38.r2.dev/git/timer-logger/timer.png)
![invoice](https://pub-2d4798b44c2c47d1adecc6c62bf47f38.r2.dev/git/timer-logger/invoice.jpg)

Sample PDF Invoice Link: https://pub-2d4798b44c2c47d1adecc6c62bf47f38.r2.dev/git/timer-logger/invoice.pdf

</details>

# TODO

- [ ] Add support for authentication and multiple users
  - The backend is already designed to support multiple users, but the frontend currently assumes a single user. This will require changes to the frontend to allow users to log in and manage their own projects and time entries.
  - This may include creating a roles (admins, managers and users) with a filament dashboard, and allow a hierarchy of users management, but for now the focus is on the single user experience.
- [ ] Add UI for restoring deleted resources
  - The backend already supports soft deletes for projects, time frames, and time entries, but the frontend currently does not have a UI for restoring deleted resources. This will require changes to the frontend to allow users to view and restore deleted resources.
- [ ] Add support for more advanced billing features and criteria.
- [ ] Add support for localization and multiple languages
- [ ] Add support for exporting data in different formats (e.g. CSV, PDF)
- [ ] Add support for more advanced invoice customization

Fancy stuff that can be added in the future:
- [ ] Add analytics and reporting features to help users track their time and productivity
- [ ] Add support for integrating with calendar applications (e.g. Google Calendar) to automatically log time based on calendar events
- [ ] Add support for integrating with project management tools (e.g. Trello, Asana) to automatically log time based on tasks and projects
- [ ] Grow to compete Clockify and Toggl.

# Contributing

Contributions are welcome! Please open an issue or submit a pull request with your changes or your feature request.


# SUPPORT THE PROJECT
If you find this project useful and want to support its development, you can star it, share it with whoever you think might benefit from it, or even contribute to the project by submitting a pull request with new features, bug fixes, or improvements. Your support is greatly appreciated!

# License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
