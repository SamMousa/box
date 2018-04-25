<?php

declare(strict_types=1);

/*
 * This file is part of the box project.
 *
 * (c) Kevin Herrera <kevin@herrera.io>
 *     Théo Fidry <theo.fidry@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace KevinGH\Box;

use KevinGH\Box\Compactor\Php;
use Phar;
use function file_put_contents;
use function KevinGH\Box\FileSystem\remove;

/**
 * @covers \KevinGH\Box\Configuration
 */
class ConfigurationFileNoConfigTest extends ConfigurationTestCase
{
    public function test_all_the_files_found_in_the_composer_json_are_taken_by_default_with_no_config_file_is_used(): void
    {
        touch('file0');
        touch('file1');
        touch('file2');

        mkdir('B');
        touch('B/fileB0');
        touch('B/fileB1');

        mkdir('PSR4_0');
        touch('PSR4_0/file0');
        touch('PSR4_0/file1');

        mkdir('PSR4_1');
        touch('PSR4_1/file0');
        touch('PSR4_1/file1');

        mkdir('PSR4_2');
        touch('PSR4_2/file0');
        touch('PSR4_2/file1');

        mkdir('DEV_PSR4_0');
        touch('DEV_PSR4_0/file0');
        touch('DEV_PSR4_0/file1');

        mkdir('PSR0_0');
        touch('PSR0_0/file0');
        touch('PSR0_0/file1');

        mkdir('PSR0_1');
        touch('PSR0_1/file0');
        touch('PSR0_1/file1');

        mkdir('PSR0_2');
        touch('PSR0_2/file0');
        touch('PSR0_2/file1');

        mkdir('DEV_PSR0_0');
        touch('DEV_PSR0_0/file0');
        touch('DEV_PSR0_0/file1');

        mkdir('CLASSMAP_DIR');
        touch('CLASSMAP_DIR/file0');
        touch('CLASSMAP_DIR/file1');

        mkdir('CLASSMAP_DEV_DIR');
        touch('CLASSMAP_DEV_DIR/file0');
        touch('CLASSMAP_DEV_DIR/file1');

        file_put_contents(
            'composer.json',
            <<<'JSON'
{
    "autoload": {
        "files": ["file0", "file1"],
        "psr-4": {
            "Acme\\": "PSR4_0",
            "Bar\\": ["PSR4_1", "PSR4_2"]
        },
        "psr-0": {
            "Acme\\": "PSR0_0",
            "Bar\\": ["PSR0_1", "PSR0_2"]
        },
        "classmap": ["CLASSMAP_DIR"]
    },
    "autoload-dev": {
        "files": ["file2"],
        "psr-4": {
            "Acme\\": "DEV_PSR4_0"
        },
        "psr-0": {
            "Acme\\": "DEV_PSR0_0"
        },
        "classmap": ["CLASSMAP_DEV_DIR"]
    }
}
JSON
        );

        // Relative to the current working directory for readability
        $expected = [
            'CLASSMAP_DIR/file0',
            'CLASSMAP_DIR/file1',
            'PSR0_0/file0',
            'PSR0_0/file1',
            'PSR0_1/file0',
            'PSR0_1/file1',
            'PSR0_2/file0',
            'PSR0_2/file1',
            'PSR4_0/file0',
            'PSR4_0/file1',
            'PSR4_1/file0',
            'PSR4_1/file1',
            'PSR4_2/file0',
            'PSR4_2/file1',
            'composer.json',
            'file0',
            'file1',
        ];

        $noFileConfig = $this->getNoFileConfig();

        $actual = $this->normalizeConfigPaths($noFileConfig->getFiles());

        $this->assertEquals($expected, $actual);
        $this->assertCount(0, $noFileConfig->getBinaryFiles());

        $this->reloadConfig();

        $actual = $this->normalizeConfigPaths($this->config->getFiles());

        $this->assertEquals($expected, $actual);
        $this->assertCount(0, $this->config->getBinaryFiles());
    }

    public function test_the_blacklist_setting_is_applied_to_all_the_files_found_in_the_current_directory_are_taken_by_default_if_no_file_setting_is_used(): void
    {
        touch('file0');
        touch('file1');
        touch('file2');

        mkdir('B');
        touch('B/fileB0');
        touch('B/fileB1');

        mkdir('PSR4_0');
        touch('PSR4_0/file0');
        touch('PSR4_0/file1');

        mkdir('PSR4_1');
        touch('PSR4_1/file0');
        touch('PSR4_1/file1');

        mkdir('PSR4_2');
        touch('PSR4_2/file0');
        touch('PSR4_2/file1');

        mkdir('DEV_PSR4_0');
        touch('DEV_PSR4_0/file0');
        touch('DEV_PSR4_0/file1');

        mkdir('PSR0_0');
        touch('PSR0_0/file0');
        touch('PSR0_0/file1');

        mkdir('PSR0_1');
        touch('PSR0_1/file0');
        touch('PSR0_1/file1');

        mkdir('PSR0_2');
        touch('PSR0_2/file0');
        touch('PSR0_2/file1');

        mkdir('DEV_PSR0_0');
        touch('DEV_PSR0_0/file0');
        touch('DEV_PSR0_0/file1');

        mkdir('CLASSMAP_DIR');
        touch('CLASSMAP_DIR/file0');
        touch('CLASSMAP_DIR/file1');

        mkdir('CLASSMAP_DEV_DIR');
        touch('CLASSMAP_DEV_DIR/file0');
        touch('CLASSMAP_DEV_DIR/file1');

        file_put_contents(
            'composer.json',
            <<<'JSON'
{
    "autoload": {
        "files": ["file0", "file1"],
        "psr-4": {
            "Acme\\": "PSR4_0",
            "Bar\\": ["PSR4_1", "PSR4_2"]
        },
        "psr-0": {
            "Acme\\": "PSR0_0",
            "Bar\\": ["PSR0_1", "PSR0_2"]
        },
        "classmap": ["CLASSMAP_DIR"]
    },
    "autoload-dev": {
        "files": ["file2"],
        "psr-4": {
            "Acme\\": "DEV_PSR4_0"
        },
        "psr-0": {
            "Acme\\": "DEV_PSR0_0"
        },
        "classmap": ["CLASSMAP_DEV_DIR"]
    }
}
JSON
        );

        $this->setConfig([
            'blacklist' => [
                'file1',
            ],
        ]);

        // Relative to the current working directory for readability
        $expected = [
            'CLASSMAP_DIR/file0',
            'PSR0_0/file0',
            'PSR0_1/file0',
            'PSR0_2/file0',
            'PSR4_0/file0',
            'PSR4_1/file0',
            'PSR4_2/file0',
            'composer.json',
            'file0',
        ];

        $actual = $this->normalizeConfigPaths($this->config->getFiles());

        $this->assertEquals($expected, $actual);
        $this->assertCount(0, $this->config->getBinaryFiles());

        $actual = $this->normalizeConfigPaths($this->config->getFiles());

        $this->assertEquals($expected, $actual);
        $this->assertCount(0, $this->config->getBinaryFiles());
    }

    public function test_it_ignores_the_most_common_non_needed_files_when_guess_the_files_from_the_composer_json_file(): void
    {
        // Depending on the test machine: the following command might be needed:
        // docker run -i --rm -w /opt/box -v "$PWD":/opt/box box_php72 bin/phpunit tests/ConfigurationTest.php --filter test_it_ignores_the_most_common_non_needed_files_when_guess_the_files_from_the_composer_json_file
        // Indeed OSX is case insensitive...

        touch('main.php~');
        touch('main.php.back');
        touch('main.php.swp');

        touch('phpunit.xml.dist');
        touch('phpunit.xml');
        touch('phpunit_infection.xml.dist');

        touch('LICENSE');
        touch('LICENSE.md');
        touch('license');

        touch('README');
        touch('README.md');
        touch('README_ru.md');
        touch('README.rst');
        touch('readme');

        touch('UPGRADE');
        touch('UPGRADE.md');
        touch('upgrade');

        touch('CHANGELOG');
        touch('ChangeLog-7.1.md');
        touch('CHANGELOG.md');
        touch('changelog');

        touch('CONTRIBUTING');
        touch('CONTRIBUTING.md');
        touch('contributing');

        touch('TODO');
        touch('TODO.md');
        touch('todo');

        touch('CONDUCT');
        touch('CONDUCT.md');
        touch('conduct');

        touch('CODE_OF_CONDUCT.md');

        touch('AUTHORS');
        touch('AUTHORS.md');
        touch('authors');

        touch('MainTest.php');

        touch('Makefile');

        mkdir('doc');
        touch('doc/file0');

        mkdir('docs');
        touch('docs/file0');

        mkdir('src');
        touch('src/.fileB0');
        touch('src/foo.php');
        touch('src/doc.md');
        touch('src/doc.rst');
        touch('src/composer.json');

        mkdir('test');
        touch('test/file0');

        mkdir('tests');
        touch('tests/file0');

        mkdir('travis');
        touch('travis/install-ev.sh');

        mkdir('.travis');
        touch('.travis/install-ev.sh');

        touch('.travis.yml');
        touch('appveyor.yml');

        touch('phpdoc.dist.xml');
        touch('phpdoc.xml');

        touch('Vagrantfile');

        touch('phpstan.neon.dist');
        touch('phpstan.neon');
        touch('phpstan-test.neon');

        touch('infection.json.dist');
        touch('infection.json');

        touch('humbug.json.dist');
        touch('humbug.json');

        touch('easy-coding-standard.neon');
        touch('easy-coding-standard.neon.dist');

        touch('phpbench.json.dist');
        touch('phpbench.json');

        touch('phpcs.xml.dist');
        touch('phpcs.xml');

        touch('.php_cs.dist');
        touch('.php_cs');
        touch('.php_cs.cache');

        touch('scoper.inc.php.dist');
        touch('scoper.inc.php');

        mkdir('example');
        touch('example/file0');

        mkdir('examples');
        touch('examples/file0');

        mkdir('build');
        touch('build/file0');

        mkdir('dist');
        touch('dist/file0');

        mkdir('specs');
        touch('specs/file0');

        mkdir('spec');
        touch('spec/MainSpec.php');

        mkdir('features');
        touch('features/acme.feature');

        touch('build.xml.dist');
        touch('build.xml');

        touch('.editorconfig');
        touch('.gitattributes');
        touch('.gitignore');

        touch('behat.yml.dist');
        touch('behat.yml');

        touch('box.json.dist');
        touch('box.json');
        touch('box_dev.json');

        touch('Dockerfile');

        touch('codecov.yml.dist');
        touch('codecov.yml');

        touch('.styleci.yml.dist');
        touch('.styleci.yml');

        touch('.scrutiziner.yml.dist');
        touch('.scrutiziner.yml');

        file_put_contents(
            'composer.json',
            <<<'JSON'
{
    "autoload": {
        "classmap": ["./"]
    }
}
JSON
        );

        // Relative to the current working directory for readability
        $expected = [
            'composer.json',
            'src/foo.php',
        ];

        $noFileConfig = $this->getNoFileConfig();

        $actual = $this->normalizeConfigPaths($noFileConfig->getFiles());

        $this->assertEquals($expected, $actual);
        $this->assertCount(0, $noFileConfig->getBinaryFiles());

        $this->reloadConfig();

        $actual = $this->normalizeConfigPaths($this->config->getFiles());

        $this->assertEquals($expected, $actual);
        $this->assertCount(0, $this->config->getBinaryFiles());
    }

    public function test_the_existing_phars_are_ignored_when_all_the_files_are_collected(): void
    {
        touch('index.phar');

        // Relative to the current working directory for readability
        $expected = [];

        $this->reloadConfig();

        $actual = $this->normalizeConfigPaths($this->config->getFiles());

        $this->assertEquals($expected, $actual);
        $this->assertCount(0, $this->config->getBinaryFiles());

        remove('index.phar');
        touch('default');

        // Relative to the current working directory for readability
        $expected = [];

        $this->setConfig([
            'output' => 'default',
        ]);

        $actual = $this->normalizeConfigPaths($this->config->getFiles());

        $this->assertEquals($expected, $actual);
        $this->assertCount(0, $this->config->getBinaryFiles());
    }

    public function test_the_box_debug_directory_is_always_excluded(): void
    {
        touch('file0');
        touch('file1');

        mkdir('.box_dump');
        touch('.box_dump/file0');
        touch('.box_dump/file1');

        mkdir('A');
        touch('A/fileA0');
        touch('A/fileA1');

        file_put_contents(
            'composer.json',
            <<<'JSON'
{
    "autoload": {
        "classmap": ["./"]
    }
}
JSON
        );

        // Relative to the current working directory for readability
        $expected = [
            'A/fileA0',
            'A/fileA1',
            'composer.json',
            'file0',
            'file1',
        ];

        $noFileConfig = $this->getNoFileConfig();

        $actual = $this->normalizeConfigPaths($noFileConfig->getFiles());

        $this->assertEquals($expected, $actual);
        $this->assertCount(0, $noFileConfig->getBinaryFiles());
    }
}