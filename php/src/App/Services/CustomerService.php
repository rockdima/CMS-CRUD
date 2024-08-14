<?php

namespace App\Services;

use App\Repositories\CustomerRepository;

class CustomerService {

    function __construct(private CustomerRepository $customerRepository) {
    }

    function getAll(): array {
        return $this->customerRepository->getAll();
    }

    function getById(int $id): array {
        return $this->customerRepository->getById($id);
    }

    function create(array $body): bool {
        return $this->customerRepository->create($body);
    }

    function delete(int $id): bool {
        return $this->customerRepository->delete($id);
    }

    function update(int $id, array $body): bool {
        return $this->customerRepository->update($id, $body);
    }
}
