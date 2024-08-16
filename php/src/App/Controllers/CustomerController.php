<?php

namespace App\Controllers;

use App\Services\CustomerService;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;
use Valitron\Validator;

class CustomerController {

    private $body;

    function __construct(ServerRequest $request, private CustomerService $customerService, private Validator $validator) {
        $this->body = $request->getParsedBody();
    }

    /**
     * Get all customers
     */
    public function getAll() {
        return new JsonResponse([
            'status'    => 'success',
            'msg'       => 'Success',
            'data'      => $this->customerService->getAll()
        ], 200);
    }

    /**
     * Get customer by ID
     * @param int $id Customer ID
     */
    public function read(int $id) {
        if(is_numeric($id)) {
            $customer = $this->customerService->getById($id);
            if(count($customer)) {
                return new JsonResponse([
                    'status'    => 'success',
                    'msg'       => 'Success',
                    'data'      => [$customer]
                ], 200);
            }
            return new JsonResponse([
                'status'    => 'error',
                'msg'       => 'Customer not found',
                'data'      => []
            ], 200);
            
        }

        return new JsonResponse([
            'status'    => 'error',
            'msg'       => 'ID must be an integer',
            'data'      => []
        ], 200);
    }

    /**
     * Create new customer
     */
    public function create() {
        $this->validator->mapFieldsRules([
            'name'      => ['required', ['lengthMin', 3], ['lengthMax', 50], 'alpha'],
            'email'     => ['required', 'email', ['lengthMax', 50]],
            'address'   => ['required', ['lengthMax', 100]],
            'phone'     => ['required', 'numeric', ['length', 10]]
        ]);
        $this->validator = $this->validator->withData($this->body);

        if (!$this->validator->validate()) {
            return new JsonResponse([
                'status'    => 'error',
                'msg'       => 'Validation Failed',
                'data'      => $this->validator->errors()
            ], 200);
        }

        if ($this->customerService->create($this->body)) {
            return new JsonResponse([
                'status'    => 'success',
                'msg'       => 'Success',
                'data'      => ['redirect' => '/customers']
            ], 200);
        }

        return new JsonResponse([
            'status'    => 'error',
            'msg'       => 'Failed to create new customer',
            'data'      => []
        ], 200);
    }

    /**
     * Delete customer by ID
     * @param int $id Customer ID
     */
    public function delete(int $id) {
        if(is_numeric($id)) {
            if($this->customerService->delete($id)) {
                return new JsonResponse([
                    'status'    => 'success',
                    'msg'       => "Customer #{$id} successfully deleted",
                    'data'      => []
                ], 200);
            }
            return new JsonResponse([
                'status'    => 'error',
                'msg'       => 'Customer not found',
                'data'      => []
            ], 200);
        }

        return new JsonResponse([
            'status'    => 'error',
            'msg'       => 'ID must be an integer',
            'data'      => []
        ], 200);
    }

    /**
     * Update customer by ID
     * @param int $id Customer ID
     */
    public function update(int $id) {
        $this->validator->mapFieldsRules([
            'name'      => ['required', ['lengthMin', 3], ['lengthMax', 50], 'alpha'],
            'email'     => ['required', 'email', ['lengthMax', 50]],
            'address'   => ['required', ['lengthMax', 100]],
            'phone'     => ['required', 'numeric', ['length', 10]]
        ]);
        $this->validator = $this->validator->withData($this->body);

        if (!$this->validator->validate()) {
            return new JsonResponse([
                'status'    => 'error',
                'msg'       => 'Validation Failed',
                'data'      => $this->validator->errors()
            ], 200);
        }

        if ($this->customerService->update($id, $this->body)) {
            return new JsonResponse([
                'status'    => 'success',
                'msg'       => 'Success',
                'data'      => ['redirect' => '/customers']
            ], 200);
        }

        return new JsonResponse([
            'status'    => 'error',
            'msg'       => 'Failed to update',
            'data'      => []
        ], 200);
    }
}
