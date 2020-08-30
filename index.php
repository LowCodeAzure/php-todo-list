<?php
include('config.php');
?>

<!DOCTYPE html>
<html lang="en-us">
<head>
    <title>todo list</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="./style.css">

</head>

<body>

<div>
    <header>
        <p>Web Development & Programming, Spring 2020</p>
    </header>
</div>

<div id="content">

<h1 id="welcome">TO-DO LIST</h1>

<div class="register">
    <div class="col" id="col1">
    <form method="POST" action="signup.php">
        Username: <input type="text" name="username" required> <br><br>
        Password: <input type="password" name="pw" required> <br><br>
        <input type="submit" value="Sign up" id="signup">
        <?php
        if (isset($_GET['signup']) && $_GET['signup'] === 'failed') {
            print "<p class='err'>Username already exists.</p>";
        }

        if (isset($_GET['username']) && $_GET['username'] === 'invalid') {
            print "<p class='err'>Username must be alphanumeric.</p>";
        }
        ?>
    </form>
    </div>

    <div class="col" id="col2">
    <form method="POST" action="login.php">
        Username: <input type="text" name="username" required> <br><br>
        Password: <input type="password" name="pw" required> <br><br>
        <input type="submit" value="Log in" id="login">
        <?php
        if (isset($_GET['login']) && $_GET['login'] === 'failed')
            print "<p class='err'>Wrong username or password.</p>";
        ?>
    </form>
    </div>

</div>

<div id="main" class="hidden">

    <input type="submit" value="Log out" id="logout">

    <form>
        <input type="text" name="task" id="task" required>
        <input type="submit" value="Add" id="add">
    </form>

    <ul id="list">
        <script src="jquery-3.5.1.min.js"></script>
        <script>
        $(document).ready(function() {
            const register = document.querySelector('.register')
            const main = document.getElementById('main')

            // src: https://developer.mozilla.org/en-US/docs/Web/API/Document/cookie
            let user = document.cookie.replace(/(?:(?:^|.*;\s*)user\s*\=\s*([^;]*).*$)|^.*$/, "$1")

            if (user) {
                register.classList.add('hidden')
                main.classList.remove('hidden')
                const welcome = document.getElementById('welcome')
                welcome.innerText = user + "'s to-do list"
            }

            const logout = document.getElementById('logout')
            logout.addEventListener('click', (event) => {
                event.preventDefault()
                // src: https://www.w3schools.com/js/js_cookies.asp
                document.cookie = 'user=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=index.php/;'
                location.reload()
            })

            const task = document.getElementById('task')
            const add = document.getElementById('add')
            const list = document.getElementById('list')
            const clear = document.getElementById('clear')

            add.addEventListener('click', (event) => {
                event.preventDefault()
                const task_value = task.value
                const BreakException = {}

                $.ajax({
                    url: 'data/' + user + '.txt',
                    type: 'GET',
                    success: function(data, status) {
                        console.log('ajax success')
                        const lines = data.split('\n')
                        let duplicate = false
                        lines.forEach((line) => {
                            if (line) {
                                const task_data = line.split(',')[0]
                                if (task_data === task_value) {
                                    alert('Task already exists.')
                                    task.value = ''
                                    duplicate = true
                                    throw BreakException
                                }
                            }
                        })

                        if (!duplicate) {
                            $.ajax({
                                url: 'add.php',
                                type: 'POST',
                                data: {
                                    task: task_value,
                                    user: user
                                },
                                success: function(data, status) {
                                    task.value = ''
                                    const li = document.createElement('li')
                                    const text = document.createElement('span')
                                    text.classList.add('text')
                                    text.innerText = task_value
                                    li.appendChild(text)

                                    const span = document.createElement('span')
                                    span.innerText = '×'
                                    span.classList.add('delete')
                                    span.addEventListener('click', remove)

                                    li.appendChild(span)
                                    li.addEventListener('click', strike)
                                    list.appendChild(li)
                                },
                                error: function(request, data, status) {
                                    console.log('error')
                                }
                            })
                        }
                    },
                    error: function(request, data, status) {
                        console.log('error')
                    }
                })
            })

            clear.addEventListener('click', (event) => {
                event.preventDefault()
                $.ajax({
                    url:'clear.php',
                    type: 'POST',
                    data: {
                        user: user
                    },
                    success: function(data, status) {
                        while (list.firstChild) {
                            list.removeChild(list.lastChild);
                        }
                    },
                    error: function(request, data, status) {
                        console.log('error')
                    }
                })

            })

            function getList() {
                $.ajax({
                    url: 'data/' + user + '.txt',
                    type: 'GET',
                    success: function(data, status) {
                        while (list.firstChild) {
                            list.removeChild(list.lastChild);
                        }
                        const lines = data.split('\n')
                        lines.forEach((line) => {
                            if (line) {
                                const task = line.split(',')[0]
                                const li = document.createElement('li')
                                
                                const text = document.createElement('span')
                                text.classList.add('text')
                                text.innerText = task
                                li.appendChild(text)

                                if (line.split(',')[1] === 'yes') {
                                    li.classList.add('strike')
                                }

                                const span = document.createElement('span')
                                span.innerText = '×'
                                span.classList.add('delete')

                                span.addEventListener('click', remove)
                                li.appendChild(span)

                                list.appendChild(li)

                                li.addEventListener('click', strike)

                            }
                        })
                    },
                    error: function(request, data, status) {
                        console.log('error')
                    }
                })
            }

            function strike(event) {
                event.stopPropagation()
                const li = this
                let strike
                const task = li.querySelector('.text').innerText

                if (li.classList.contains('strike')) {
                    li.classList.remove('strike')
                    strike = 'no'
                } else {
                    li.classList.add('strike')
                    strike = 'yes'
                }

                $.ajax({
                    url: 'update.php',
                    type: 'POST',
                    data: {
                        task: task,
                        strike: strike,
                        user: user
                    },
                    success: function(data, status) {
                        console.log('strike success')
                    },
                    error: function(request, data, status) {
                        console.log('strike error')
                    }
                })
            }

            // delete a task
            function remove(event) {
                event.stopPropagation()
                const span = this
                const li = span.parentElement
                let strike
                const task = li.querySelector('.text').innerText

                if (li.classList.contains('strike')) {
                    strike = 'no'
                } else {
                    strike = 'yes'
                }

                $.ajax({
                    url: 'delete.php',
                    type: 'POST',
                    data: {
                        task: task,
                        strike: strike,
                        user: user
                    },
                    success: function(data, status) {
                        console.log('delete success')
                        li.style.display = 'none'
                    },
                    error: function(request, data, status) {
                        console.log('delete error')
                    }
                })
            }

            getList()

        })

        </script>

    </ul>

    <form>
        <input type="submit" value="Clear all" id="clear">
    </form>

</div>

</div>

<div>
    <footer>
        <p>&copy; Sylvia Ji</p>
    </footer>
</div>


</body>

</html>