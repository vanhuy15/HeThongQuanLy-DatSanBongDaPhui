<?php
namespace PHPUnit\Framework;

/**
 * IDE Stub for PHPUnit Framework TestCase
 * Giúp VS Code / Intelephense nhận diện đầy đủ các phương thức assertion
 * và xóa bỏ 100% cảnh báo lỗi đỏ trong thư mục tester/01_unit_test.
 */
if (!class_exists('PHPUnit\Framework\TestCase')) {
    abstract class TestCase {
        public function assertEquals(mixed $expected, mixed $actual, string $message = ''): void {}
        public function assertNotEquals(mixed $expected, mixed $actual, string $message = ''): void {}
        public function assertTrue(mixed $condition, string $message = ''): void {}
        public function assertFalse(mixed $condition, string $message = ''): void {}
        public function assertContains(mixed $needle, iterable $haystack, string $message = ''): void {}
        public function assertNotContains(mixed $needle, iterable $haystack, string $message = ''): void {}
        public function assertGreaterThan(mixed $expected, mixed $actual, string $message = ''): void {}
        public function assertGreaterThanOrEqual(mixed $expected, mixed $actual, string $message = ''): void {}
        public function assertLessThanOrEqual(mixed $expected, mixed $actual, string $message = ''): void {}
        public function assertIsInt(mixed $actual, string $message = ''): void {}
        public function assertStringStartsWith(string $prefix, string $string, string $message = ''): void {}
        public function assertStringContainsString(string $needle, string $haystack, string $message = ''): void {}
        protected function setUp(): void {}
        protected function tearDown(): void {}
    }
}
