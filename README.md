

# Cli command library
*Библиотека для создания cli команд*

Для генерации автозагрузчика запустите команду 
```
composer dump-autoload
```

Для работы с библиотекой используйте 
```
php bin/app.php
```

Название команды передается первым аргументом в произвольном формате. *Названием команды является название класса регистрируемой команды в нижнем регистре*

Аргументы запуска передаются в фигурных скобках через запятую в следующем
формате:
- одиночный аргумент: {arg}
- несколько аргументов: {arg1,arg2,arg3} ИЛИ {arg1} {arg2} {arg3}
ИЛИ {arg1,arg2} {arg3}
- параметры запуска передаются в квадратных скобках в следующем формате:
- параметр с одним значением: [name=value]
- параметр с несколькими значениями: [name={value1,value2,value3}]

Функциональность библиотеки включает в себя:
- регистрацию необходимых команд в приложении;
- установку описания команды, её параметров и аргументов;
- обработку входящих параметров;
- выполнение заданной логики с возможностью вывода в информации в консоль

При запуске любой из команд с аргументом {help} будет выведенно описание
команды и список допустимых входящих аргументов и параметров.
 
#### Пример запуска команды:
```sh
$/usr/bin/php bin/app.php out {order} [select=all]
```

Для создания новой команды необходимо создать класс реализующий интерфейс CommandInterface.
Команду можно сделать предустановленной путем помещения в папку src/Console/Preset, тогда при подгрузке предустановленных команд она будет доступна. Так же можно зарегистрировать команду в классе **CommandManager в методе register()**

#### Пример:
```sh
$commandManager = new Library\CommandManager($argv, false);
$commandManager->register(new MyNewCommand($commandManager->getInput(), $commandManager->getOutput()));
```

