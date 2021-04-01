<?php

namespace Enum;

use InvalidArgumentException;

class Generator
{
    protected array $namespace;
    protected string $class;

    protected array $options;

    const BLANK = '';

    const REGEXP_CLASS = '/^ \\\\ ( (?: \w+ \\\\ )* ) ( \w+ ) $/ux';
    const REGEXP_PROP = '/^ [A-Z] \w* $/ux';
    const REGEXP_TYPE = '/^ ( \\\\ \w+ )* \w+ $/ux';

    public static function checkClass(string $class)
    {
        return preg_match(self::REGEXP_CLASS, $class, $match) ? $match : null;
    }

    public static function checkProp(string $prop)
    {
        return preg_match(self::REGEXP_PROP, $prop, $match) ? $match : null;
    }

    public static function checkType(string $type)
    {
        return preg_match(self::REGEXP_TYPE, $type, $match) ? $match : null;
    }

    public function __construct(string $class, array $options)
    {
        if ($match = self::checkClass($class)) {
            $this->namespace = explode('\\', $match[1]);
            array_pop($this->namespace);
            $this->class = $match[2];
        } else {
            throw new InvalidArgumentException("Invalid class: `$class`");
        }

        foreach ($options as $name => $type) {
            if (is_null(self::checkProp($name))) {
                throw new InvalidArgumentException("Invalid property: `$name`");
            }
            if (!is_null($type) and is_null(self::checkType($type))) {
                throw new InvalidArgumentException("Invalid type: `$type`");
            }
        }

        $this->options = $options;
    }

    public static function build(string $class, array $options): \Generator
    {
        $self = new self($class, $options);
        return $self->iterLines();
    }

    public function hasNamespace(): bool
    {
        return count($this->namespace) > 0;
    }

    public function getNamespace(): string
    {
        return implode('\\', $this->namespace);
    }

    public function getFullname(): string
    {
        return $this->hasNamespace() ? $this->getNamespace() . '\\' . $this->class : $this->class;
    }

    public function getBasepath(): string
    {
        return $this->hasNamespace() ? implode(DIRECTORY_SEPARATOR, $this->namespace) : '.';
    }

    public function getFilename(string $extension = 'php'): string
    {
        return $this->getBasepath() . DIRECTORY_SEPARATOR . $this->class . '.' . $extension;
    }

    public function saveIntoFile(string $rootpath, string $extension = 'php')
    {
        if (substr($rootpath, -1) !== DIRECTORY_SEPARATOR) {
            $rootpath .= DIRECTORY_SEPARATOR;
        }
        $path = $rootpath . $this->getBasepath();
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $file = $rootpath . $this->getFilename($extension);
        try {
            $fh = fopen($file, 'w');
            if ($fh === false or !is_resource($fh)) {
                throw new InvalidArgumentException("Cannot open file `$file` for writing");
            }
            fputs($fh, '<?php' . PHP_EOL . PHP_EOL . 'declare(strict_types=1);' . PHP_EOL . PHP_EOL);
            foreach ($this->iterLines() as $line) {
                fputs($fh, $line . PHP_EOL);
            }
        } finally {
            if (is_resource($fh)) {
                fclose($fh);
            }
        }
    }

    public function iterOptions(bool $typed_only = false): \Generator
    {
        $index = 0;
        $fullname = $this->getFullname();
        foreach ($this->options as $name => $type) {
            $index++;
            $typed = !is_null($type);
            if ($typed_only and !$typed) {
                continue;
            }
            $string = var_export("$fullname::$name", true);
            $var = $typed ? "\${$name}" : '';
            $arg = $typed ? "{$type} {$var}" : '';
            $prop_name = $typed ? "_{$name}" : null;
            $this_prop_name = $typed ? "\$this->{$prop_name}" : null;
            $prop_type = $typed ? "{$type} \${$prop_name}" : null;
            yield [
                'name'           => $name,
                'string'         => $string,
                'type'           => $type,
                'typed'          => $typed,
                'index'          => $index,
                'var'            => $var,
                'arg'            => $arg,
                'prop_name'      => $prop_name,
                'this_prop_name' => $this_prop_name,
                'prop_type'      => $prop_type,
            ];
        }
    }

    public function iterLines(): \Generator
    {
        $state_prop = "state";
        $this_state_prop = "\$this->{$state_prop}";

        if ($this->hasNamespace()) {
            yield "namespace {$this->getNamespace()};";
            yield self::BLANK;
        }

        yield "final class {$this->class}";
        yield "{";

        foreach ($this->iterOptions() as $option) {
            yield "\tconst {$option['name']} = {$option['string']};";
        }

        yield self::BLANK;

        yield "\tprivate string \${$state_prop};";

        foreach ($this->iterOptions(true) as $option) {
            yield "\tprivate ?{$option['prop_type']};";
        }

        yield self::BLANK;

        yield "\tprivate function __construct()";
        yield "\t{";
        yield "\t\t{$this_state_prop} = '';";

        foreach ($this->iterOptions(true) as $option) {
            yield "\t\t\$this->{$option['prop_name']} = null;";
        }
        yield "\t}";

        yield self::BLANK;

        yield "\tpublic function __toString(): string";
        yield "\t{";
        yield "\t\treturn {$this_state_prop};";
        yield "\t}";

        foreach ($this->iterOptions() as $option) {
            yield self::BLANK;

            yield "\t/**";
            yield "\t * Construct a new <em>{$option['name']}</em> instance";
            yield "\t *";
            if ($option['typed']) {
                yield "\t * @param {$option['arg']}";
            }
            yield "\t * @return self";
            yield "\t */";
            yield "\tpublic static function {$option['name']}({$option['arg']}): self";
            yield "\t{";
            yield "\t\t\$self = new self();";
            yield "\t\t\$self->set{$option['name']}({$option['var']});";
            yield "\t\treturn \$self;";
            yield "\t}";

            yield self::BLANK;
            yield "\t/**";
            yield "\t * Returns `true` if state is <em>{$option['name']}</em>, otherwise `false`";
            yield "\t *";
            yield "\t * @return bool";
            yield "\t */";
            yield "\tpublic function is{$option['name']}(): bool";
            yield "\t{";
            yield "\t\treturn {$this_state_prop} === self::{$option['name']};";
            yield "\t}";

            if ($option['typed']) {
                yield self::BLANK;
                yield "\t/**";
                yield "\t * Get inner value if state is <em>{$option['name']}</em>, otherwise `\$default`";
                yield "\t *";
                yield "\t * @param {$option['type']}|null \$default Default value when state is not <em>{$option['name']}</em>";
                yield "\t * @return {$option['type']}|null";
                yield "\t */";
                yield "\tpublic function get{$option['name']}(?{$option['type']} \$default = null): ?{$option['type']}";
                yield "\t{";
                yield "\t\treturn \$this->is{$option['name']}() ? \$this->{$option['prop_name']} : \$default;";
                yield "\t}";
            }

            yield self::BLANK;
            yield "\t/**";
            yield "\t * Set state to <em>{$option['name']}</em>";
            yield "\t *";
            if ($option['typed']) {
                yield "\t * @param {$option['arg']} Inner value";
            }
            yield "\t * @return void";
            yield "\t */";
            yield "\tpublic function set{$option['name']}({$option['arg']}): void";
            yield "\t{";
            foreach ($this->iterOptions(true) as $suboption) {
                yield "\t\t\$this->{$suboption['prop_name']} = null;";
            }
            yield "\t\t{$this_state_prop} = self::{$option['name']};";
            if ($option['typed']) {
                yield "\t\t{$option['this_prop_name']} = {$option['var']};";
            }
            yield "\t}";

            yield self::BLANK;
            yield "\t/**";
            yield "\t * Call function if state is <em>{$option['name']}</em>";
            yield "\t *";
            yield "\t * @param callable \$fn({$option['arg']})";
            yield "\t * @param mixed|null \$default Default value when state is not <em>{$option['name']}</em>";
            yield "\t * @return mixed|null Return value of `\$fn()` or `\$default`";
            yield "\t */";
            yield "\tpublic function when{$option['name']}(callable \$fn, \$default = null)";
            yield "\t{";
            yield "\t\treturn \$this->is{$option['name']}() ? \$fn({$option['this_prop_name']}) : \$default;";
            yield "\t}";
        }
        yield "}";
    }
}
