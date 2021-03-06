<?php
/*
 * Copyright (C) 2011-2013 Geoff Adams <geoff@dianode.net>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace BedRest\TestFixtures\Models\Company;

use BedRest\Resource\Mapping\Annotation as BedRest;

/**
 * Employee
 *
 * This model uses protected properties and magic getters and setters. It also
 * does not have an explicit resource name set, so it should be auto-generated
 * by BedREST.
 *
 * @author Geoff Adams <geoff@dianode.net>
 *
 * @BedRest\Resource
 * @BedRest\Handler(
 *      service="BedRest\TestFixtures\Services\Company\Employee"
 * )
 */
class Employee
{
    /**
     * ID reference.
     * @var integer
     */
    protected $id;

    /**
     * Name of the employee.
     * @var string
     */
    protected $name;

    /**
     * Date of birth of the employee.
     * @var \DateTime
     */
    protected $dob;

    /**
     * Whether the employee is active or not.
     * @var boolean
     */
    protected $active;

    /**
     * Employee salary.
     * @var float
     */
    protected $salary;

    /**
     * Assets associated with this employee.
     * @var \Doctrine\Common\Collections\Collection
     * @BedRest\SubResource(name="assets", service="EmployeeAssetsService")
     */
    protected $Assets;

    /**
     * Department this employee belongs to.
     * @var \BedRest\TestFixtures\Models\Company\Department
     */
    protected $Department;

    /**
     * Magic setter.
     * @param string $property
     * @param mixed  $value
     */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
     * Magic getter.
     * @param  string $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->$property;
    }
}
