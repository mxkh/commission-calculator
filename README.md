# Install application
1. Clone repository
2. Go to project directory`cd commission-calculator`
3. Run `composer i`

# Run application
1. To run app, please, run following command from CLI `php public/index.php "input.csv" "https://your.exhcange.rates.url"`
2. You will see the application output

# Test application
- To test app with unit tests only you can run `composer run phpunit`
- To test app source code with CSFixer only you can run `composer run test-cs`
- to test both app and app source code you can run `composer run test`

Please note, before run php cs fixer you may need to create a config file with name `.php-cs-fixer.php` and copy\paste content from `.php-cs` file
