<?php
session_start();

require_once 'connection.php';

$link = mysqli_connect($host, $user, $password, $database)
    or die("Ошибка " . mysqli_error($link));

$flag = 0;

if(isset($_POST['add']))// Добавить заказ
{
    request();
}else
    if(isset($_POST['detail']))// Подробнее о заказе
    {
        if(isset($_POST['select']))
            {
                $result1=mysqli_query($link,"SELECT Task FROM requests where IDRequest = ".$_POST['select']."");
                $myrow= mysqli_fetch_array($result1);
                deteilRequest($myrow[0]);
            }else{}
    }
    else
        if(isset($_POST['backUser']))// Назад для пользователя
        {
            request();
        }else
            if(isset($_POST['back']))// Назад для сотрудника
            {
                $req=mysqli_query($link,"SELECT * FROM requests where LoginEmployee is NULL");
                choiceRequest($req);

            }else
                if(isset($_POST['finish']))// Завершение
                {
                    if (mysqli_query($link,"UPDATE requests SET Done = 1 WHERE IDRequest = '".$_POST['select']."';")) {
                                            echo "New record created successfully";
                                            yourRequests($result);
                                        } else 
                                            {
                                                echo "Error: " . $sql . "<br>" . mysqli_error($link);
                                            }

                }else
                    if(isset($_POST['transition']))// Страничка с выбранными работами
                    {
                        $result=mysqli_query($link,"SELECT * FROM requests where LoginEmployee = '".$_SESSION['login']."' and Done = 0");
                        yourRequests($result);
                    }else
                        if(isset($_POST['take']))// Страничка с выбором
                            {
                                if(isset($_POST['select']))
                                {
                                    if (mysqli_query($link,"UPDATE requests SET LoginEmployee = '".$_SESSION['login']."' WHERE IDRequest = ".$_POST['select'].";")) {
                                            echo "New record created successfully";
                                            $result=mysqli_query($link,"SELECT * FROM requests where LoginEmployee = '".$_SESSION['login']."'");
                                            yourRequests($result);
                                        } else 
                                            {
                                                echo "Error: " . $sql . "<br>" . mysqli_error($link);
                                            }
                                }
                            }else
                                if(isset($_POST['request'])) //Страничка с запросом
                                {
                                    if (mysqli_query($link,"INSERT INTO requests(LoginUser, Task, DateTimeRequest) VALUES ('".$_SESSION['login']."','".$_POST["task"]."','".date("Y-m-d H:i:s")."');")) {
                                        usersListFunc($link,'Новая заявка оставлена');
                                    } else 
                                        {
                                            echo "Error: " . $sql . "<br>" . mysqli_error($link);
                                        }
                                } else 
                                    if(isset( $_POST['registryDone'] ) ) //Страничка с регистрацией
                                    {
                                        if($_POST["password"] != $_POST["password2"])
                                        {
                                            registy("Пароли не совпадают");
                                        }else
                                            if($_POST["login"] == '' || $_POST["password"] == '' ||$_POST["name"] == '' ||$_POST["surname"] == '' ||$_POST["patronymic"] == '' ||$_POST["email"] == '' ||$_POST["phoneNumber"] == '')
                                            {
                                                registy("Вы заполнили не все поля");
                                            }else
                                                if (mysqli_query($link,"INSERT INTO users VALUES ('".$_POST["login"]."', '".$_POST["password"]."',"
                                                        . " '".$_POST["name"]."', '".$_POST["surname"]."', '".$_POST["patronymic"]."', "
                                                        ." '".$_POST["email"]."', '".$_POST["phoneNumber"]."', 0);"))
                                                {
                                                    input("Новый пользователь добавлен");
                                                }
                                        
                                    
                                    }else
                                        if(isset( $_POST['registry'])) // Страничка входа
                                            {
                                                registy();
                                            }else 
                                                if(isset( $_POST['input']))
                                                {
                                                    $result=mysqli_query($link,"SELECT * FROM users");
                                                    while($myrow= mysqli_fetch_array($result))
                                                    {
                                                        $i = 0;
                                                        if($myrow[0] == $_POST["login"] && $myrow[1] == $_POST["password"])
                                                        {
                                                            $_SESSION['login'] = $_POST["login"];
                                                            if($myrow[7] == 0)
                                                            {
                                                                $flag = 1;
                                                                usersListFunc($link);
                                                                break;

                                                            }else
                                                            {
                                                                $flag = 1;
                                                                choiceRequestFunc($link);
                                                                break;
                                                            }
                                                        }

                                                    }
                                                    if ($flag == 0)
                                                    {
                                                        input('Неправильный логий или пароль');
                                                    }
                                                } else 
                                                    {
                                                        inputFunc();
                                                    }


                                                    //



        function choiceRequestFunc($link)
        {
            $req=mysqli_query($link,"SELECT * FROM requests where LoginEmployee is NULL and Done = 0");
            choiceRequest($req);
        }

        function inputFunc()
        {
            session_unset();
            session_destroy(); // Завершение сессии
            input(); //Стартовый экран
        }

        function usersListFunc($link,$str = '')
        {
            $req=mysqli_query($link,"SELECT * FROM requests where LoginUser = '".$_SESSION['login']."' and Done = 0");  
            usersList($req,$str);
        }


        function registy($str = '')
        {
            echo "
                <html>
                <head>
                    <title>Регистрация</title>
                    <style>
                        div {textalign: center}
                        table td:first-child{text-align: right;}
                        body { background: url(https://images.wallpaperscraft.ru/image/fon_tekstura_pyatna_bleklyy_50634_2048x1152.jpg); }
                    </style>
                    <meta charset=\"UTF-8\">
                    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
                </head>
                <body>
                    <div align = 'center'>
                        <FORM action=\"index.php\" method=\"post\">
                            <h1> Регистрация нового пользователя</h1>
                            <table align = 'center' border=\"0\" width=\"25%\" cellpadding=\"5\">
                               <tr>
                                <td>Логин</td>
                                <td><input type='text' name='login'></td>
                               </tr>
                               <tr>
                                <td>Пароль</td>
                                <td><input type='password' name='password'</td>
                              </tr>
                              <tr>
                                <td>Подтвердите пароль</td>
                                <td><input type='password' name='password2'</td>
                              </tr>
                              <tr>
                                <td>Имя</td>
                                <td><input type='text' name='name'></td>
                              </tr>
                              <tr>
                                <td>Фамилия</td>
                                <td><input type='text' name='surname'</td>
                              </tr>
                              <tr>
                                <td>Отчество</td>
                                <td><input type='text' name='patronymic'</td>
                              </tr>
                              <tr>
                                <td>Email</td>
                                <td><input type='text' name='email'</td>
                              </tr>
                              <tr>
                                <td>Номер телефона</td>
                                <td><input type='text' name='phoneNumber'</td>
                              </tr>
                             </table>
                            <p>
                                <input type=\"submit\" name='registryDone' value=\"Отправить\">
                                <input type=\"reset\" value=\"Сброс\">
                                <input type=\"submit\" name='backRegisty' value=\"Назад\">
                            </p>
                            <p>".$str."</p>
                        </FORM>
                    </div>
                </body>
            </html>";
            return 0;
        }

        
        function request()
        {

            echo "<html>
                    <head>
                        <title>Заявка</title>
                        <style>
                            div {text-align: center}
                            body { background: url(https://images.wallpaperscraft.ru/image/fon_tekstura_pyatna_bleklyy_50634_2048x1152.jpg); }
                        </style>
                        <meta charset=\"UTF-8\">
                        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
                    </head>
                    <body>
                        <div>
                            <FORM action=\"index.php\" method=\"post\">
                                <h1>Заполнение заявки</h1>
                                <p><textarea cols=\"50\" rows=\"20\" name=\"task\">Напишите вашу заявку</textarea></p>

                                <p>
                                    <input type=\"submit\" name='request' value=\"Отправить\">
                                    <input type=\"reset\" value=\"Сброс\">
                                    <input type=\"submit\" name='exit' value=\"Выход\">
                                </p>

                            </FORM>
                        </div>
                    </body>
                </html>
                    ";
            return 0;
        }
        
        function choiceRequest($result)
        {
            
            echo "<html>
                    <head>
                        <title>Выбор запроса</title>
                        <style>
                            div {text-align: center}
                            select {width: 846px; height: 263px}
                            body { background: url(https://images.wallpaperscraft.ru/image/fon_tekstura_pyatna_bleklyy_50634_2048x1152.jpg); }
                        </style>
                        <meta charset=\"UTF-8\">
                        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
                    </head>
                    <body>
                        <div>
                            <FORM action=\"index.php\" method=\"post\">
                                <h1>Выбор запроса от пользователя</h1>
                                <select name=\"select\" size=\"10\" multiple>";

            while($myrow= mysqli_fetch_array($result))
            {
                echo "<option value=".$myrow[0].">".$myrow[2]."</option>";
            }             
                        echo "</select>

                                <p>
                                    <input type=\"submit\" name='detail' value=\"Подробнее\">
                                    <input type=\"submit\" name = \"transition\" value=\"Перейти в личный кабинет\">
                                    <input type=\"submit\" name='exit' value=\"Выход\">
                                </p>
                            </FORM>

                        </div>
                    </body>
                </html>
                ";
            return 0;
        }

         function deteilRequest($result)
        {
            
            echo "<html>
                    <head>
                        <title>Выбор запроса</title>
                        <style>
                            div {text-align: center}
                            textarea {width: 846px; height: 263px}
                            body { background: url(https://images.wallpaperscraft.ru/image/fon_tekstura_pyatna_bleklyy_50634_2048x1152.jpg); }
                        </style>
                        <meta charset=\"UTF-8\">
                        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
                    </head>
                    <body>
                        <div>
                            <FORM action=\"index.php\" method=\"post\">
                                <h1>Выбор запроса от пользователя</h1>";

                                echo "<p><textarea name=\"detail\" disabled readonly>".$result."</textarea></p>";


                                echo "<p>
                                    <input type=\"submit\" name = \"take\" value=\"Взять\">
                                    <input type=\"submit\" name='back' value=\"Назад\">
                                    <input type=\"submit\" name='exit' value=\"Выход\">
                                </p>
                            </FORM>

                        </div>
                    </body>
                </html>
                ";
            return 0;
        }

        function usersList($result, $str = '')
        {
            
            echo "<html>
                    <head>
                        <title>Выбор запроса</title>
                        <style>
                            div {text-align: center}
                            textarea {select: 846px; height: 263px}
                            body { background: url(https://images.wallpaperscraft.ru/image/fon_tekstura_pyatna_bleklyy_50634_2048x1152.jpg); }
                        </style>
                        <meta charset=\"UTF-8\">
                        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
                    </head>
                    <body>
                        <div>
                            <FORM action=\"index.php\" method=\"post\">
                                <h1>Выбор запроса от пользователя</h1>
                                <select name=\"select\" size=\"10\" multiple>";

                        while($myrow= mysqli_fetch_array($result))
                        {
                            echo "<option value=".$myrow[0].">".$myrow[2]."</option>";
                        }       

                        echo "</select>
                        <p>
                                    <input type=\"submit\" name = \"add\" value=\"Добавить\">
                                    <input type=\"submit\" name='exit' value=\"Выход\">
                                </p>
                        <p>".$str."</p>
                            </FORM>

                        </div>
                    </body>
                </html>
                ";
            return 0;
        }

        function yourRequests($result)
        {
            
            echo "<html>
                    <head>
                        <title>Выбор запроса</title>
                        <style>
                            div {text-align: center}
                            select {width: 846px; height: 263px}
                            body { background: url(https://images.wallpaperscraft.ru/image/fon_tekstura_pyatna_bleklyy_50634_2048x1152.jpg); }
                        </style>
                        <meta charset=\"UTF-8\">
                        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
                    </head>
                    <body>
                        <div>
                            <FORM action=\"index.php\" method=\"post\">
                                <h1>Выбранные запросы</h1>
                                <select name=\"select\" size=\"10\" multiple>";

            while($myrow= mysqli_fetch_array($result))
            {
                echo "<option value=".$myrow[0].">".$myrow[2]."</option>";
            }             
                        echo "</select>
                                <p>
                                    <input type=\"submit\" name = \"finish\" value=\"Закончить\">
                                    <input type=\"submit\" name='detail' value=\"Подробнее\">
                                    <input type=\"submit\" name='exit' value=\"Выход\">
                                    <input type=\"submit\" name='back' value=\"Назад\">
                                </p>
                            </FORM>
                        </div>
                    </body>
                </html>
                ";
            return 0;
        }
        
        function input($str = '')
        {
            
            echo '
                <html>
                <head>
                    <title>Система заявок на помощь в написании курсовых/дипломных работ</title>
                    <style>
                        div {text-align: center}
                        body { background: url(https://images.wallpaperscraft.ru/image/fon_tekstura_pyatna_bleklyy_50634_2048x1152.jpg); }
                    </style>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                </head>
                <body>
                    <div>
                    <FORM action="index.php" method="post">
                    <h1>Вход с систему</h1>
                    <p>Логин <input type=\'text\' name=\'login\'></p>
                    <p>Пароль <input type=\'password\' name=\'password\'></p>
                    <p>
                    <input type="submit" name = "input" value="Войти">
                    <input type="reset" value="Сброс">
                    <input type="submit" name = "registry" value="Регистрация" >
                    </p>
                    <p>'.$str.'</p>

                    </div>
                </body>
            </html>
            ';
            return 0;
        }
        
