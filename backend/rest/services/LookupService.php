<?php

require_once __DIR__ . '/BaseService.php';

/** Genres and platforms: a unique name and an optional description. */
abstract class LookupService extends BaseService
{
    protected array $fields = ['name', 'description'];

    /** The row with this name, or null/false. */
    abstract protected function find_by_name(string $name);

    /** Why the row cannot be deleted (it is still in use), or null if it can. */
    abstract protected function delete_blocker($id): ?string;

    protected function allowed(array $data): array
    {
        return $this->empty_to_null(parent::allowed($data), ['description']);
    }

    protected function validate(array $data, ?int $id = null): void
    {
        Validator::required($data, ['name']);
        Validator::length($data['name'], 'name', 2, 60);
        Validator::no_html($data['name'], 'name');
        if (!empty($data['description'])) {
            Validator::length($data['description'], 'description', 0, 255);
            Validator::no_html($data['description'], 'description');
        }
        $existing = $this->find_by_name($data['name']);
        if ($existing && (int) $existing[$this->dao->get_primary_key()] !== (int) $id) {
            throw new ValidationException("A {$this->entity_name_lower()} named '{$data['name']}' already exists.");
        }
    }

    public function delete($id): void
    {
        $this->get_by_id($id);
        $reason = $this->delete_blocker($id);
        if ($reason !== null) {
            throw new ValidationException($reason);
        }
        $this->dao->delete($id);
    }

    protected function entity_name_lower(): string
    {
        return strtolower($this->entity_name);
    }
}
