<?php

namespace Tustin\PlayStation\Enums;

/**
 * GraphQL operations that are supported by the PlayStation API.
 */
enum GraphqlOperation: string
{
    case MetGetConceptByProductIdQuery = 'metGetConceptByProductIdQuery';
    case MetGetConceptById = 'metGetConceptById';
    case MetGetProductById = 'metGetProductById';
    case MetGetAddOnsByTitleId = 'metGetAddOnsByTitleId';
    case MetGetCategoryGrid = 'metGetCategoryGrid';
    case MetGetCategoryGrids = 'metGetCategoryGrids';
    case MetGetCategoryStrands = 'metGetCategoryStrands';
    case MetGetDefaultView = 'metGetDefaultView';
    case MetGetPricingDataByConceptId = 'metGetPricingDataByConceptId';
    case MetGetStoreWishlist = 'metGetStoreWishlist';
    case MetGetViews = 'metGetViews';
    case MetGetWebCheckoutCart = 'metGetWebCheckoutCart';
    case MetGetExperience = 'metGetExperience';
    case MetGetContextSearchResults = 'metGetContextSearchResults';
    case MetGetDomainSearchResults = 'metGetDomainSearchResults';

    case GetPurchasedGameList = 'getPurchasedGameList';

    public function hash(): string
        {
            return match ($this) {
                self::MetGetConceptByProductIdQuery => '0a4c9f3693b3604df1c8341fdc3e481f42eeecf961a996baaa65e65a657a6433',
                self::MetGetConceptById => 'cc90404ac049d935afbd9968aef523da2b6723abfb9d586e5f77ebf7c5289006',
                self::MetGetProductById => 'a128042177bd93dd831164103d53b73ef790d56f51dae647064cb8f9d9fc9d1a',
                self::MetGetAddOnsByTitleId => 'e98d01ff5c1854409a405a5f79b5a9bcd36a5c0679fb33f4e18113c157d4d916',
                self::MetGetCategoryGrid => 'b67a9e4414b80d8d762bf12a588c6125467ae0bb3bbe3cee3f7696c6984f8ef6',
                self::MetGetCategoryGrids => 'cc0b6513521c59a321bf62334fa23a92f22cd2ce1abe9f014fadac6379e414a8',
                self::MetGetCategoryStrands => '55ab5f168bec56f8362b5519f59faaf786d4e1cfeabb8bc969d6a65545e14f4d',
                self::MetGetDefaultView => 'bec1b8a3b0bae8c08e3ce2c7fe2f38a69343434ccfbcdd82cc1f2e44f86b7c40',
                self::MetGetPricingDataByConceptId => 'abcb311ea830e679fe2b697a27f755764535d825b24510ab1239a4ca3092bd09',
                self::MetGetStoreWishlist => '571149e8aa4d76af7dd33b92e1d6f8f828ebc5fa8f0f6bf51a8324a0e6d71324',
                self::MetGetViews => '6fd98ff7fecb603006fb5d92db176d5028435be163c8d1ee9f7c598ab4677dd1',
                self::MetGetWebCheckoutCart => '2d4165c4de76877a32f3d08c91ce2af0e01d69300131fed0a8022868235e85b1',
                self::MetGetExperience => '054e61ee68bbeadc21435caebcc4f2bba0919a99b06629d141b0b82dc55f10c4',
                self::MetGetContextSearchResults => 'ac5fb2b82c4d086ca0d272fba34418ab327a7762dd2cd620e63f175bbc5aff10',
                self::MetGetDomainSearchResults => '23ece284bf8bdc50bfa30a4d97fd4d733e723beb7a42dff8c1ee883f8461a2e1',
                self::GetPurchasedGameList => '827a423f6a8ddca4107ac01395af2ec0eafd8396fc7fa204aaf9b7ed2eefa168',
                
            };
        }
}