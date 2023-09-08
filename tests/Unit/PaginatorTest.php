<?php

namespace Constellix\Client\Tests\Unit;

use Constellix\Client\Pagination\Factories\PaginatorFactory;
use Constellix\Client\Pagination\Paginator;

class PaginatorTest extends TestCase
{
    protected PaginatorFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new PaginatorFactory();
    }

    public function testPaginatorFactory(): void
    {
        $page = $this->factory->paginate([], 0, 20);
        $this->assertInstanceOf(Paginator::class, $page);
    }

    public function testIterator(): void
    {
        $page = $this->factory->paginate([], 0, 20);
        $this->assertInstanceOf(\ArrayIterator::class, $page->getIterator());
        $this->assertIsIterable($page);
    }

    public function testEmptyPage(): void
    {
        $page = $this->factory->paginate([], 0, 20);
        $this->assertEquals(0, $page->count());
        $this->assertNull($page->firstItem());
        $this->assertNull($page->lastItem());
        $this->assertTrue($page->onFirstPage());
        $this->assertFalse(($page->hasMorePages()));
    }

    public function testInitialSetup(): void
    {
        $page = $this->factory->paginate(['Apple', 'Orange', 'Banana'], 9, 3, 2);
        $this->assertCount(3, $page->items());
        $this->assertEquals(['Apple', 'Orange', 'Banana'], $page->items());
        $this->assertEquals(3, $page->count());

        $this->assertEquals(9, $page->total());
        $this->assertEquals(3, $page->perPage());
        $this->assertEquals(2, $page->currentPage());

        $this->assertEquals(3, $page->lastPage());
    }

    public function testPages(): void
    {
        $page = $this->factory->paginate(['Apple', 'Orange', 'Banana'], 9, 3, 1);
        $this->assertTrue($page->onFirstPage());
        $this->assertTrue($page->hasMorePages());
        $this->assertEquals(1, $page->firstItem());
        $this->assertEquals(3, $page->lastItem());

        $page = $this->factory->paginate(['Apple', 'Orange', 'Banana'], 9, 3, 2);
        $this->assertFalse($page->onFirstPage());
        $this->assertTrue($page->hasMorePages());
        $this->assertEquals(4, $page->firstItem());
        $this->assertEquals(6, $page->lastItem());

        $page = $this->factory->paginate(['Apple', 'Orange', 'Banana'], 9, 3, 3);
        $this->assertFalse($page->onFirstPage());
        $this->assertFalse($page->hasMorePages());
        $this->assertEquals(7, $page->firstItem());
        $this->assertEquals(9, $page->lastItem());
    }

    public function testOffset(): void
    {
        $page = $this->factory->paginate(['Apple', 'Orange', 'Banana'], 9, 3, 2);
        $this->assertTrue($page->offsetExists(0));
        $this->assertTrue($page->offsetExists(2));
        $this->assertFalse($page->offsetExists(3));

        $this->assertEquals($page->offsetGet(0), 'Apple');
        $this->assertEquals($page->offsetGet(1), 'Orange');
        $this->assertEquals($page->offsetGet(2), 'Banana');

        $page->offsetUnset(1);
        $this->assertCount(2, $page->items());
        $this->assertEquals('Apple', $page->offsetGet(0));
        $this->assertEquals('Banana', $page->offsetGet(2));

        $page->offsetSet(1, 'Mango');
        $this->assertCount(3, $page->items());
        $this->assertEquals('Mango', $page->offsetGet(1));
    }
}
