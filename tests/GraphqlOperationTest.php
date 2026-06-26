<?php

use Tustin\PlayStation\Enums\GraphqlOperation;

it('provides a valid hash for every graphql operation', function (): void {
    foreach (GraphqlOperation::cases() as $operation) {
        if ($operation === GraphqlOperation::GetRemoteDownloadList) {
            continue;
        }

        $hash = $operation->hash();

        expect($hash)->toMatch('/^[a-f0-9]{64}$/');
    }
});

it('currently has no hash for GetRemoteDownloadList', function (): void {
    GraphqlOperation::GetRemoteDownloadList->hash();
})->throws(\UnhandledMatchError::class);

it('includes the expected mutation operations', function (): void {
    $mutations = GraphqlOperation::AddToCart->getMutationOperations();

    expect($mutations)->toContain(GraphqlOperation::AddToCart);
    expect($mutations)->toContain(GraphqlOperation::RemoveFromCart);
    expect($mutations)->toContain(GraphqlOperation::AddToWishlist);
    expect($mutations)->toContain(GraphqlOperation::RemoveFromWishlist);
});
