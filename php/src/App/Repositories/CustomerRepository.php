<?php

namespace App\Repositories;

use App\utils\db\DBInterface;

class CustomerRepository {
    function __construct(private DBInterface $db) {
    }

    /**
     * Get all customers
     * 
     * @return array
     */
    function getAll(): array {
        $query = "SELECT * FROM customers";
        return $this->db->fetch($query, []);
    }

     /**
     * Get customer by ID
     * 
     * @return array
     */
    function getById(int $id): array {
        $query = "SELECT * FROM customers WHERE id=:id";
        $params = [':id' => $id];
        return $this->db->fetch($query, $params);
    }

     /**
     * Create new customer
     * 
     * @param array $body form data
     * @return bool
     */
    function create($body): bool {
        $query = "INSERT INTO customers
                    (name, email, address, phone)
                    VALUES (:name, :email, :address, :phone)";
        $params = [':name' => $body['name'], ':email' => $body['email'], ':address' => $body['address'], ':phone' => $body['phone']];
        return $this->db->execute($query, $params);
    }

     /**
     * Delete customer by ID
     * 
     * @param int $id customer ID
     * @return bool
     */
    function delete(int $id): bool {
        $query = "DELETE FROM customers WHERE ID=:ID";
        $params = [':ID' => $id];
        return $this->db->execute($query, $params);
    }

     /**
     * Update customer by ID
     * 
     * @param int $id customer ID
     * @param array $body form data
     * @return bool
     */
    function update(int $id, array $body): bool {
        $query = "UPDATE customers SET
                    name=:name, email=:email, address=:address, phone=:phone
                    WHERE ID=:ID";
        $params = [':name' => $body['name'], ':email' => $body['email'], ':address' => $body['address'], ':phone' => $body['phone'], ':ID' => $id];
        return $this->db->execute($query, $params);
    }
}
