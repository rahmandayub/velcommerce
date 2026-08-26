<?php

use App\Enums\OrderStatus;

test('pending can transition to paid, cancelled, and failed', function () {
    expect(OrderStatus::Pending->allowedTransitions())->toBe([
        OrderStatus::Paid,
        OrderStatus::Cancelled,
        OrderStatus::Failed,
    ]);
});

test('paid can transition to shipped and cancelled', function () {
    expect(OrderStatus::Paid->allowedTransitions())->toBe([
        OrderStatus::Shipped,
        OrderStatus::Cancelled,
    ]);
});

test('shipped can only transition to completed', function () {
    expect(OrderStatus::Shipped->allowedTransitions())->toBe([OrderStatus::Completed]);
});

test('completed, cancelled, and failed are terminal states', function () {
    foreach ([OrderStatus::Completed, OrderStatus::Cancelled, OrderStatus::Failed] as $status) {
        expect($status->allowedTransitions())->toBe([]);
    }
});

test('canTransitionTo rejects invalid transitions', function (OrderStatus $from, OrderStatus $to) {
    expect($from->canTransitionTo($to))->toBeFalse();
})->with([
    fn (): array => [OrderStatus::Pending, OrderStatus::Shipped],
    fn (): array => [OrderStatus::Paid, OrderStatus::Paid],
    fn (): array => [OrderStatus::Cancelled, OrderStatus::Paid],
    fn (): array => [OrderStatus::Failed, OrderStatus::Pending],
    fn (): array => [OrderStatus::Completed, OrderStatus::Cancelled],
]);
