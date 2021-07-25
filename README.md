# todo-api
Small api for a todo list made with laravel 8

Users can sign up and create tasks that can have many steps

This api is meant to be used for a [desktop application](https://github.com/Ola-jed/QTodo) and a [mobile app](https://github.com/Ola-jed/todolist)

## Project setup
```bash
# Clone the repository
git clone https://github.com/Ola-jed/todo-api.git
cd todo-api
# Create the database todo_api in your dbms
# The other configurations are done in this script
./setup.sh
# If you serve, you can use the api with the desktop or the mobile app
```

## Endpoints

| __Method__ | __Url__               | __Action__                                               |
|------------|-----------------------|----------------------------------------------------------|
| POST       | /signup               | Register to the app                                      |
| POST       | /signin               | Login a user                                             |
| POST       | /logout               | Logout the user                                          |
| POST       | /password-reset       | Reset the user's password                                |
| GET        | /account              | Get the user account information                         |
| PUT        | /account              | Update the user's account                                |
| DELETE     | /account              | Delete the account                                       |
| GET        | /tasks                | Get the tasks created by the user (pagination available) |
| GET        | /tasks/finished       | Get all the finished tasks                               |
| GET        | /tasks/unfinished     | Get all the tasks not yet finished                       |
| GET        | /tasks/expired        | Retrieve tasks that are past their due date              |
| GET        | /tasks/search/{title} | Search all the tasks corresponding to the title          |
| POST       | /tasks                | Create a new task                                        |
| GET        | /tasks/{slug}         | Get the corresponding task                               |
| PUT        | /tasks/{slug}/finish  | Update the finish status of the task                     |
| PUT        | /tasks/{slug}         | Update a task                                            |
| DELETE     | /tasks/{slug}         | Delete a task                                            |
| GET        | /tasks/{slug}/steps   | Get all steps of a task                                  |
| GET        | /steps/{id}           | Get a specific step                                      |
| PUT        | /tasks/{slug}/finish  | Update a step                                            |
| PUT        | /steps/{id}/finish    | Set a step finished or not                               |
| DELETE     | /steps/{id}           | Delete a step                                            |
| GET        | /token-check          | Check if the given token is valid for further requests   |
