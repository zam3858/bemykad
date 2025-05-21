[![Latest Version on Packagist](https://img.shields.io/packagist/v/zam3858/bemykad.svg?style=flat-square)](https://packagist.org/packages/zam3858/bemykad) [![Fix PHP code style issues](https://github.com/zam3858/bemykad/actions/workflows/fix-php-code-style-issues.yml/badge.svg)](https://github.com/zam3858/bemykad/actions/workflows/fix-php-code-style-issues.yml) [![PHPStan](https://github.com/zam3858/bemykad/actions/workflows/phpstan.yml/badge.svg)](https://github.com/zam3858/bemykad/actions/workflows/phpstan.yml) [![Tests](https://github.com/zam3858/bemykad/actions/workflows/run-tests.yml/badge.svg)](https://github.com/zam3858/bemykad/actions/workflows/run-tests.yml) [![Total Downloads](https://img.shields.io/packagist/dt/zam3858/bemykad.svg?style=flat-square)](https://packagist.org/packages/zam3858/bemykad)

# BeMyKad

`BeMyKad` is a PHP package for extracting information from Malaysian MyKad numbers. This package helps validate MyKad numbers, retrieve the date of birth, gender, and identify the state or country of birth from the MyKad number.

## Installation

To install the package, use Composer:

```bash
composer require zam3858/bemykad
```

## Framework Integration

This package includes service providers and bundles for easy integration with popular PHP frameworks.

### Laravel

1.  **Service Provider Registration:**
    After installing the package, Laravel's package auto-discovery feature should automatically register the `BeMyKadServiceProvider`. If you have disabled auto-discovery or need to register it manually, add the service provider to the `providers` array in your `config/app.php` file:

    ```php
    'providers' => [
        // ...
        BeMyKad\Laravel\BeMyKadServiceProvider::class,
    ],
    ```

2.  **Usage:**
    You can resolve the `BeMyKad` class from the service container. Since `BeMyKad` requires the MyKad number during instantiation, you need to pass it as a parameter:

    ```php
    <?php

    namespace App\Http\Controllers;

    // Example in a Laravel Controller
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\App; // Or use the global app() helper

    // ...
    public function handleMyKad(Request $request)
    {
        $mykadNumber = $request->input('mykad_number');
        if (!$mykadNumber) {
            return response()->json(['error' => 'MyKad number is required'], 400);
        }

        try {
            $myKad = App::make(\BeMyKad\BeMyKad::class, ['mykadNumber' => $mykadNumber]);
            // Or $myKad = app('bemykad', ['mykadNumber' => $mykadNumber]);

            if ($myKad->isValid()) {
                return response()->json([
                    'formattedMyKad' => $myKad->getFormattedMyKad(),
                    'dateOfBirth' => $myKad->getDateOfBirth(),
                    'gender' => $myKad->getGender() === \BeMyKad\BeMyKad::MALE ? 'Male' : 'Female',
                    'state' => $myKad->getState(),
                ]);
            } else {
                return response()->json(['error' => 'Invalid MyKad number'], 422);
            }
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    ```

### Symfony

1.  **Bundle Registration:**
    After installing the package, register the `BeMyKadBundle` in your `config/bundles.php` file (for Symfony 4/5+):

    ```php
    // config/bundles.php
    return [
        // ... other bundles
        BeMyKad\Symfony\BeMyKadBundle\BeMyKadBundle::class => ['all' => true],
    ];
    ```

2.  **Usage:**
    The bundle registers `BeMyKad\BeMyKad` as a non-shared service (prototype). This means a new instance is created each time it's requested, and it requires the `mykadNumber` argument for instantiation. For dynamic `mykadNumber` values (e.g., from a request), direct instantiation within your controller or service is often the clearest approach.

    ```php
    <?php
    // src/Controller/MyKadController.php
    namespace App\Controller;

    use BeMyKad\BeMyKad;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;

    class MyKadController extends AbstractController
    {
        /**
         * @Route("/mykad_info/{mykadNumber}", name="mykad_info")
         */
        public function show(string $mykadNumber): Response
        {
            try {
                $myKad = new BeMyKad($mykadNumber);

                if ($myKad->isValid()) {
                    $info = [
                        'formatted' => $myKad->getFormattedMyKad(),
                        'dob' => $myKad->getDateOfBirth(),
                        'gender' => $myKad->getGender() === BeMyKad::MALE ? 'Male' : 'Female',
                        'state' => $myKad->getState(),
                    ];
                    return $this->json($info);
                } else {
                    return $this->json(['error' => 'Invalid MyKad number.'], Response::HTTP_BAD_REQUEST);
                }
            } catch (\InvalidArgumentException $e) {
                return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
            }
        }
    }
    ```
    **Note:** While direct instantiation is shown, the service `BeMyKad\BeMyKad` (aliased from `bemykad.mykad_factory`) is available from the container. You can configure services in your `services.yaml` to utilize it via dependency injection, especially if you have a way to supply the `mykadNumber` argument during service construction (e.g., using a custom factory or service decorators if the MyKad number is known at compile time or comes from a fixed source).

### CodeIgniter 4

Integrating BeMyKad into your CodeIgniter 4 project is straightforward.

1.  **Installation:**
    If you haven't already, install the package via Composer:

    ```bash
    composer require zam3858/bemykad
    ```

2.  **Usage in Controllers or Services:**
    Thanks to PSR-4 autoloading, which CodeIgniter 4 uses, you can directly use the `BeMyKad\BeMyKad` class in your controllers, models, or services after installation. There's no special CodeIgniter-specific service registration required for basic usage.

    Simply import the class and instantiate it:

    #### Example Controller:

    ```php
    <?php
    // app/Controllers/MyKadController.php
    namespace App\Controllers;

    use BeMyKad\BeMyKad; // Import the class

    class MyKadController extends BaseController // Ensure BaseController is used or adjust as needed
    {
        public function process(string $mykadNumber = null)
        {
            if (empty($mykadNumber)) {
                // Example: Get from POST request if not in URL
                // $mykadNumber = $this->request->getPost('mykad_number');
                // For this example, let's assume it's passed or error
                return $this->response->setStatusCode(400)->setJSON(['error' => 'MyKad number is required.']);
            }

            try {
                $myKad = new BeMyKad($mykadNumber);

                if ($myKad->isValid()) {
                    $data = [
                        'formattedMyKad' => $myKad->getFormattedMyKad(),
                        'dateOfBirth' => $myKad->getDateOfBirth(),
                        'gender' => $myKad->getGender() === BeMyKad::MALE ? 'Male' : 'Female',
                        'state' => $myKad->getState(),
                        // 'age' => $myKad->getAge(), // Assuming getAge() exists
                    ];
                    return $this->response->setJSON($data);
                } else {
                    return $this->response->setStatusCode(422)->setJSON(['error' => 'Invalid MyKad number.']);
                }
            } catch (\InvalidArgumentException $e) {
                return $this->response->setStatusCode(400)->setJSON(['error' => $e->getMessage()]);
            }
        }
    }
    ```
    Remember to adjust namespaces and use the correct base controller (e.g., `App\Controllers\BaseController`) for your specific CodeIgniter 4 application structure.

## Basic Usage (Standalone)

If you are not using a framework with specific integration, or if you prefer to use the class directly, you can instantiate `BeMyKad\BeMyKad` anywhere in your PHP project.

### Example

```php
use BeMyKad\BeMyKad;

$myKadString = "YYMMDD-PB-###G"; // Replace with an actual MyKad number, e.g., "900101-10-1234"
try {
    $myKad = new BeMyKad($myKadString);

    if ($myKad->isValid()) {
        echo "Formatted MyKad: " . $myKad->getFormattedMyKad() . "\n";
        echo "Date of Birth: " . $myKad->getDateOfBirth() . "\n";
        echo "Gender: " . ($myKad->getGender() === BeMyKad::MALE ? 'Male' : 'Female') . "\n";
        echo "State of Birth: " . $myKad->getState() . "\n";
        // echo "Age: " . $myKad->getAge() . "\n"; // If getAge() is implemented and needed
    } else {
        echo "The MyKad number is invalid.\n";
    }
} catch (\InvalidArgumentException $e) {
    // Handles cases where the MyKad string format is fundamentally wrong before validation logic
    echo "Error: " . $e->getMessage() . "\n";
}
```

### Methods

- **`__construct(string $mykadNumber)`**: Initializes the `BeMyKad` instance. It expects a MyKad number string. Throws `\InvalidArgumentException` if the input format is fundamentally invalid (e.g., contains non-numeric characters after basic cleaning, or wrong length).
- **`getRawMyKad(): string`**: Returns the cleaned MyKad number (digits only).
- **`getFormattedMyKad(): string`**: Returns the MyKad number in `YYMMDD-PB-###G` format.
- **`isValid(): bool`**: Returns `true` if the MyKad number format, date of birth, and state code are valid.
- **`getDateOfBirth(): ?string`**: Returns the date of birth in `Y-m-d` format if valid, or `null` if invalid.
- **`getGender(): ?int`**: Returns `BeMyKad::MALE` or `BeMyKad::FEMALE` if valid, or `null` if invalid.
- **`getState(): ?string`**: Returns the place of birth (state or country) if valid. For codes that don't map to a specific place or are invalid, returns `'Unknown'` or `null` from `getRawState()` if truly invalid.
- **`getAge(): ?int`**: Calculates and returns the age based on the date of birth. Returns `null` if the date of birth is invalid.
- **`__toString(): string`**: Returns a JSON representation of the MyKad data.

Constants for Gender:
- `BeMyKad::MALE`
- `BeMyKad::FEMALE`

## Validation Details

- **Constructor Validation**: The constructor performs initial checks for invalid characters and length. If these checks fail, an `\InvalidArgumentException` is thrown.
- **`isValid()` Method**: This method performs comprehensive checks including:
    - Correct MyKad number length after cleaning (12 digits).
    - Valid date of birth (e.g., not February 30th).
    - Recognizable state/country code.
- **Date of Birth Validation**: Ensures the date part of the MyKad number corresponds to a real calendar date.
- **Format Validation**: Accepts `YYMMDD-SS-SSSS` or `YYMMDDSSSSSS`.

## Handling Unmapped Codes

Certain place of birth codes (e.g., `'99'`) that represent general categories rather than specific locations will result in `getState()` returning `'Unknown'`.

## Exceptions

- **`\InvalidArgumentException`**: Thrown by the constructor if the input MyKad number string is fundamentally malformed (e.g., contains invalid characters that cannot be stripped, or is of an incorrect length after stripping dashes). Methods like `getDateOfBirth()`, `getGender()`, `getState()`, `getAge()` will return `null` if the MyKad number is structurally plausible but contains invalid date or state codes (these cases are covered by `isValid()` returning `false`).

## Additional Class: `BirthCountry`

The `BeMyKad\BirthCountry` class contains the mappings for MyKad place-of-birth codes to their respective locations. You can retrieve the mapped location directly using the following method:

- **`getPlaceOfBirth(string $code): string`**: Returns the place of birth corresponding to the provided code. If the code is not recognized, it returns `'Unknown'`.

### Example Usage of `BirthCountry`

```php
use BeMyKad\BirthCountry;

echo BirthCountry::getPlaceOfBirth('10'); // Outputs: "Selangor"
echo BirthCountry::getPlaceOfBirth('99'); // Outputs: "Unknown"
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

We welcome contributions! Please see [CONTRIBUTING](CONTRIBUTING.md) for details on how to get involved.

## Security Vulnerabilities

If you discover any security issues, please review our [security policy](../../security/policy) for reporting vulnerabilities.

## Credits

- [Hizam Mohd](https://github.com/zam3858)
- [Nasrul Hazim](https://github.com/nasrulhazim)

## License

This package is open-sourced software licensed under the [MIT License](LICENSE.md).
