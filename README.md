# Krankmeldungsformular
## Installation
Folgende Variablen müssen im Script *index.php* angepasst werden:

```php
    $recipient="tuttas@mmbbs.de";
    $upload_folder="/var/tmp/";
    $SMTPHost="smtp.gmail.com";
    $SMTPUser="tuttas68@gmail.com";
    $STMPPassword=file_get_contents("kennwort.txt");
```

Das Kennwort für den SMTP Server ist dabei ausgelagert in der Datei *kennwort.txt*.