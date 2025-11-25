<?php

namespace Tests\Type;

use CHYP\Partner\Echooss\Voucher\Type\ValueHolderTrait;
use PHPUnit\Framework\TestCase;

/**
 * ValueHolderTrait 單元測試。
 */
class ValueHolderTraitTest extends TestCase
{
    /**
     * 測試 __set 設定一般屬性。
     */
    public function testSetAssignsPropertyDirectly(): void
    {
        $instance = $this->createTraitInstance();

        $instance->testProperty = 'test value';

        $this->assertEquals('test value', $instance->testProperty);
    }

    /**
     * 測試 __set 呼叫 mutator 方法。
     */
    public function testSetCallsMutatorMethodIfExists(): void
    {
        $instance = $this->createTraitInstanceWithMutator();

        $instance->transformedValue = 'input';

        // mutator 應該將值轉換為大寫
        $this->assertEquals('INPUT', $instance->transformedValue);
    }

    /**
     * 測試 __get 取得一般屬性。
     */
    public function testGetReturnsPropertyValue(): void
    {
        $instance = $this->createTraitInstance();

        $instance->myProperty = 'my value';

        $this->assertEquals('my value', $instance->myProperty);
    }

    /**
     * 測試 __get 從 protectedData 取得值。
     */
    public function testGetReturnsProtectedDataValue(): void
    {
        $instance = $this->createTraitInstanceWithMutator();

        $instance->transformedValue = 'test';

        // 值應該在 protectedData 中
        $this->assertEquals('TEST', $instance->transformedValue);
    }

    /**
     * 測試設定多個屬性。
     */
    public function testSetMultipleProperties(): void
    {
        $instance = $this->createTraitInstance();

        $instance->prop1 = 'value1';
        $instance->prop2 = 'value2';
        $instance->prop3 = 'value3';

        $this->assertEquals('value1', $instance->prop1);
        $this->assertEquals('value2', $instance->prop2);
        $this->assertEquals('value3', $instance->prop3);
    }

    /**
     * 測試設定不同類型的值。
     */
    public function testSetDifferentValueTypes(): void
    {
        $instance = $this->createTraitInstance();

        $instance->stringValue = 'string';
        $instance->intValue = 123;
        $instance->floatValue = 12.5;
        $instance->boolValue = true;
        $instance->arrayValue = ['a', 'b', 'c'];
        $instance->nullValue = null;

        $this->assertEquals('string', $instance->stringValue);
        $this->assertEquals(123, $instance->intValue);
        $this->assertEquals(12.5, $instance->floatValue);
        $this->assertTrue($instance->boolValue);
        $this->assertEquals(['a', 'b', 'c'], $instance->arrayValue);
        $this->assertNull($instance->nullValue);
    }

    /**
     * 測試覆蓋屬性值。
     */
    public function testOverridePropertyValue(): void
    {
        $instance = $this->createTraitInstance();

        $instance->myProp = 'initial';
        $this->assertEquals('initial', $instance->myProp);

        $instance->myProp = 'updated';
        $this->assertEquals('updated', $instance->myProp);
    }

    /**
     * 測試 mutator 方法驗證邏輯。
     */
    public function testMutatorWithValidation(): void
    {
        $instance = $this->createTraitInstanceWithValidationMutator();

        $instance->positiveNumber = 5;

        $this->assertEquals(5, $instance->positiveNumber);
    }

    /**
     * 建立使用 trait 的測試實例。
     *
     * @return object
     */
    private function createTraitInstance(): object
    {
        return new class {
            use ValueHolderTrait;
        };
    }

    /**
     * 建立帶有 mutator 方法的測試實例。
     *
     * @return object
     */
    private function createTraitInstanceWithMutator(): object
    {
        return new class {
            use ValueHolderTrait;

            /**
             * Mutator 方法 - 將值轉為大寫。
             *
             * @param string $value 輸入值。
             *
             * @return string 轉換後的值。
             */
            public function transformedValue(string $value): string
            {
                return strtoupper($value);
            }
        };
    }

    /**
     * 建立帶有驗證 mutator 的測試實例。
     *
     * @return object
     */
    private function createTraitInstanceWithValidationMutator(): object
    {
        return new class {
            use ValueHolderTrait;

            /**
             * Mutator 方法 - 確保值為正數。
             *
             * @param integer $value 輸入值。
             *
             * @return integer 驗證後的值。
             */
            public function positiveNumber(int $value): int
            {
                if ($value <= 0) {
                    throw new \InvalidArgumentException('Value must be positive');
                }
                return $value;
            }
        };
    }
}

