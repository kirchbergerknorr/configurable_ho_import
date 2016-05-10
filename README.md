# Kirchbergerknorr Configurable Ho_Import
Extention of Ho_Import to make add attributes dynamically to import profiles. For detailed information how to 
use Ho_Import please visit https://github.com/ho-nl/Ho_Import.

## Installation

Add `require` and `repositories` sections to your composer.json as shown in example below and run `composer update`.

*composer.json example*

```
{
    ...
    
    "repositories": [
        {"type": "git", "url": "https://github.com/kirchbergerknorr/Kirchbergerknorr_DuplicateOrders"},
    ],
    
    "require": {
        "kirchbergerknorr/Kirchbergerknorr_DuplicateOrders": "*"
    },
    
    ...
}
```

## Usage

In Magento Backend you will find the configuration under System -> Configuration -> General -> Configurable Ho_Import.
There you can choose on the left a configured import profile, and on the right you can add your attributes dynamically.
Please note that some attributes are mandatory if a new product should be created. For further debug information if your configuration
works with your file, execute the import via shell with `php hoimport.php --action line --profile [yourprofilename]` .

## Support

Please [report](https://github.com/kirchbergerknorr/kirchbergerknorr/configurable_ho_import/issues/new) new bugs or improvements. We are aware that this module is no final solution and consider it as a beta. 
The configurable attributes are not tested yet in storeview context. We will improve this module as soon as we face new issues or requirements.