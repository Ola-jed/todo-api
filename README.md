# todo-api
    Small api for a todo list made with laravel 8
    Users can sign up and create tasks that can have many steps
    This api is meant to be used for a desktop application (QTodo)

## Project setup
```bash
# Clone the repository
git clone https://github.com/Ola-jed/todo-api.git
cd todo-api
# Create the database todo_api in your dbms
# The other configurations are done in this script
./setup.sh
# If you serve, you can use the api with the app
# 
```

## Endpoints

_**Auth routes**_

__POST__ /signup : Signup to the app

__POST__ /signin : Login to the app

__POST__ /logout : Logout the connected user

_**Tasks**_

__GET__ /tasks : Get all tasks (offset and limit can be given for pagination)

__GET__ /tasks/finished : Get all tasks finished by the user

__GET__ /tasks/unfinished : Get all tasks not yet finished by the user

__GET__ /tasks/expired : Get all the expired tasks 

__GET__ /tasks/search/{title} : Search tasks by title

__POST__ /tasks : Create a new task

__GET__ /tasks/{slug} : Get the corresponding task

__PUT__ /tasks/{slug}/finish : Set the status for the task (TODO)

__PUT__ /tasks/{slug} : Modify the corresponding task

__DELETE__ /tasks/{slug} : Delete the corresponding task

_**Steps(of some tasks)**_

__GET__ /tasks/{slug}/steps : Get all steps of a task

__GET__ /steps/{id} : Get a specific step

__PUT__ /steps/{id} : Modify a step

__PUT__ /steps/{id}/finish : Set a step finished or not

__DELETE__ /steps/{id} : Delete a step 
