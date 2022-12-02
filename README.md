## Response Builder for Laravel API

ResponseBuilder is Laravel's helper designed to build nice, normalized and easy to consume REST API JSON responses.

## Requirement

> Laravel >= 6.x
>
>  Php >= 7.2
>
> composer
>
> git

# Installation & Configuration

You can install the package via composer:

```bash
$ composer require chaos/response-builder
```

The package will automatically register its service provider for laravel 5.5.* and above.

For below version need to register a service provider manually in  `config/app.php`

```bash
'providers' => [
  /*
  * Package Service Providers...
  */
  Chaos\ResponseBuilder\ResponseBuilderServiceProvider::class
]
```

The package will automatically load alias for laravel 5.5.* and above.
For below version need to add alias manually in `config/app.php`

```bash
'providers' => [
  
  'ResponseBuilder' => Chaos\ResponseBuilder\Facades\ResponseBuilder::class,
]
```

## Usage

### Example 1

```php
use Chaos\ResponseBuilder\Facades\ResponseBuilder;

$data = [1, 2, 3, 4];
return ResponseBuilder::success($data)
    ->build();
```

See response below:

```text
{
    "meta": {
        "status":true,
        "code":200,
        "message":"OK"
    },
    "data":[1,2,3,4,5]
}
```

### Example 2

```php
use Chaos\ResponseBuilder\Facades\ResponseBuilder;

$data = [1, 2, 3, 4];
return ResponseBuilder::success($data)
    ->message('Result Message')
    ->append('custom-key','value')
    ->httpStatusCode(Response::HTTP_NO_CONTENT)
    ->build();
```

See response below:

```text
{
    "meta": {
        "status":true,
        "code":204,
        "message":"Result Message"
    },
    "data":[1,2,3,4,5],
    "custom-key": "value"
}
```

### Example 3

```php
use Chaos\ResponseBuilder\Facades\ResponseBuilder;
use Chaos\ResponseBuilder\Resources\MessageResource;

$data = collect(
    [
        "tr" => "Merhaba! Aklındaki tüm soruları sorabilirsin. En kısa sürede cevaplayacağım. 🤗",
        "en" => "Hello! You can ask all your questions. I'll answer them as soon as I can. 🤗",
        "ru" => "Здравствуйте! Вы можете задать все интересующие Вас вопросы. Я отвечу на них в ближайшее время. 🤗",
        "uk" => "Вітаю! Ви можете задати всі ваші запитання. Я відповім на них якомога швидше. 🤗",
        "es" => "¡Hola! Puedes hacer todas tus preguntas. Las responderé tan pronto como pueda 🤗",
        "de" => "Hallo! Du kannst alle deine Fragen stellen. Ich werde sie so schnell wie möglich beantworten. 🤗",
        "he" => "שלום! אתה יכול לשאול את כל השאלות שלך. אענה להם ברגע שאוכל 🤗",
        "ar" => "مرحبًا! يمكنك أن تسأل كل أسئلتك. سأجيب عليهم بأسرع ما يمكن 🤗",
        "pt" => "Olá! Você pode tirar todas as suas dúvidas. Vou respondê-las assim que puder 🤗",
        "ja" => "こんにちは！いかなる質問でもお聞きください。早急に回答いたします。 🤗",
   ]
);
return ResponseBuilder::success($data, MessageResource::class)
    ->message('Result Message')
    ->append('custom-key','value')
    ->build();
```

See response below:

```text
{
    "meta": {
        "status":true,
        "code":200,
        "message":"Result Message"
    },
    "data":[
        "tr" => "Merhaba! Aklındaki tüm soruları sorabilirsin. En kısa sürede cevaplayacağım. 🤗",
        "en" => "Hello! You can ask all your questions. I'll answer them as soon as I can. 🤗",
        "ru" => "Здравствуйте! Вы можете задать все интересующие Вас вопросы. Я отвечу на них в ближайшее время. 🤗",
        "uk" => "Вітаю! Ви можете задати всі ваші запитання. Я відповім на них якомога швидше. 🤗",
        "es" => "¡Hola! Puedes hacer todas tus preguntas. Las responderé tan pronto como pueda 🤗",
        "de" => "Hallo! Du kannst alle deine Fragen stellen. Ich werde sie so schnell wie möglich beantworten. 🤗",
        "he" => "שלום! אתה יכול לשאול את כל השאלות שלך. אענה להם ברגע שאוכל 🤗",
        "ar" => "مرحبًا! يمكنك أن تسأل كل أسئلتك. سأجيب عليهم بأسرع ما يمكن 🤗",
        "pt" => "Olá! Você pode tirar todas as suas dúvidas. Vou respondê-las assim que puder 🤗",
        "ja" => "こんにちは！いかなる質問でもお聞きください。早急に回答いたします。 🤗",
    ],
    "custom-key": "value"
}
```

### Example 4

```php
use Chaos\ResponseBuilder\Facades\ResponseBuilder;
use Chaos\ResponseBuilder\Resources\MessageResource;

$data = [
    'device_id'     => "26728172-d050-4126-8ee2-4bfe8201565c",
    'secret'        => "0184cd97-7351-7121-91cb-5a818f3eb4b0",
    'platform'      => "iOS",
    'version'       => "1.0",
    "language_code" => "en",
    "country_code"  => "TR",
    "time_zone"     => "Europe/Istanbul",
];

$validator = Validator::make($data, [
    'device_id' => 'required|size:12', // device length should be 12 chars
    'secret'    => 'required',
    'platform'  => ['required', Rule::in('Android', 'iOS', 'Huawei')],
]);

return ResponseBuilder::error($validator->errors())->build();

```

See response below:

```text
{
    "meta":{
        "status":false,
        "code":422,
        "message":"Error"
   },
   "errors":{
        "device_id":["The device id must be 12 characters."]
   }
}
```
