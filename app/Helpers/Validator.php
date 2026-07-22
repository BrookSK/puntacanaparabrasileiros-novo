<?php
declare(strict_types=1);

namespace App\Helpers;

/**
 * Classe de validação de dados.
 */
class Validator
{
    private array $errors = [];
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function make(array $data): self
    {
        return new self($data);
    }

    public function required(string $field, ?string $message = null): self
    {
        if (empty($this->data[$field]) && $this->data[$field] !== '0') {
            $this->errors[$field] = $message ?? "O campo {$field} é obrigatório.";
        }
        return $this;
    }

    public function email(string $field, ?string $message = null): self
    {
        if (!empty($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $message ?? "Email inválido.";
        }
        return $this;
    }

    public function min(string $field, int $min, ?string $message = null): self
    {
        if (!empty($this->data[$field]) && mb_strlen((string) $this->data[$field]) < $min) {
            $this->errors[$field] = $message ?? "O campo {$field} deve ter pelo menos {$min} caracteres.";
        }
        return $this;
    }

    public function max(string $field, int $max, ?string $message = null): self
    {
        if (!empty($this->data[$field]) && mb_strlen((string) $this->data[$field]) > $max) {
            $this->errors[$field] = $message ?? "O campo {$field} deve ter no máximo {$max} caracteres.";
        }
        return $this;
    }

    public function numeric(string $field, ?string $message = null): self
    {
        if (!empty($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->errors[$field] = $message ?? "O campo {$field} deve ser numérico.";
        }
        return $this;
    }

    public function confirmed(string $field, ?string $message = null): self
    {
        $confirmation = $this->data[$field . '_confirmation'] ?? '';
        if (($this->data[$field] ?? '') !== $confirmation) {
            $this->errors[$field . '_confirmation'] = $message ?? "As senhas não coincidem.";
        }
        return $this;
    }

    public function in(string $field, array $allowed, ?string $message = null): self
    {
        if (!empty($this->data[$field]) && !in_array($this->data[$field], $allowed)) {
            $this->errors[$field] = $message ?? "Valor inválido para {$field}.";
        }
        return $this;
    }

    public function date(string $field, ?string $message = null): self
    {
        if (!empty($this->data[$field])) {
            $dt = \DateTime::createFromFormat('Y-m-d', $this->data[$field]);
            if (!$dt) {
                $this->errors[$field] = $message ?? "Data inválida.";
            }
        }
        return $this;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        return $this->errors ? reset($this->errors) : null;
    }
}
