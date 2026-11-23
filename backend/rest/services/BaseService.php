<?php

require_once __DIR__ . '/../../utils/exceptions.php';
require_once __DIR__ . '/../../utils/Validator.php';
require_once __DIR__ . '/../dao/BaseDao.php';

/**
 * What every service shares: reading, adding, updating and deleting with a
 * validate() hook and an allow-list of writable columns ($fields).
 * Services never write SQL and know nothing about HTTP: they take values,
 * return values and throw HttpExceptions when a rule is broken.
 */
class BaseService
{
    /** Columns a client may write. Everything else in a request body is ignored. */
    protected array $fields = [];

    public function __construct(protected BaseDao $dao, protected string $entity_name) {}

    public function get_all(): array
    {
        return $this->dao->get_all();
    }

    /** The row, or NotFoundException ("Genre not found."). */
    public function get_by_id($id): array
    {
        $row = $this->dao->get_by_id($id);
        if (!$row) {
            throw new NotFoundException("{$this->entity_name} not found.");
        }
        return $row;
    }

    public function add(array $data): array
    {
        $data = $this->allowed($data);
        $this->validate($data);
        $created = $this->dao->add($data);
        return $this->get_by_id($created[$this->dao->get_primary_key()]);
    }

    /** Required fields must be sent again; optional fields that are left out stay unchanged. */
    public function update(array $data, $id): array
    {
        $this->get_by_id($id);              // 404 first
        $data = $this->allowed($data);
        $this->validate($data, (int) $id);
        $this->dao->update($data, $id);
        return $this->get_by_id($id);
    }

    public function delete($id): void
    {
        $this->get_by_id($id);
        $this->dao->delete($id);
    }

    /** Business rules. Throws a ValidationException when something is wrong. */
    protected function validate(array $data, ?int $id = null): void
    {
    }

    /** Allow-list + trimmed strings. */
    protected function allowed(array $data): array
    {
        $clean = array_intersect_key($data, array_flip($this->fields));
        return array_map(fn($value) => is_string($value) ? trim($value) : $value, $clean);
    }

    /** "" becomes NULL for the listed optional columns (only if they were sent). */
    protected function empty_to_null(array $data, array $fields): array
    {
        foreach ($fields as $field) {
            if (array_key_exists($field, $data) && ($data[$field] === '' || $data[$field] === null)) {
                $data[$field] = null;
            }
        }
        return $data;
    }
}
