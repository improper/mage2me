# mage2me

mage2me is a download helper for Mageno 2 Commerce and Open Source. The tool allows you to not only specify the Magento edition but also any tag or branch version available in the Magento composer repository. If that's not enough, it will also validate your Magento and Github access tokens to ensure you're on the right path.

Go ahead and grab your tokens before getting started:

- Magento: https://marketplace.magento.com/customer/accessKeys/
- Github: https://github.com/settings/tokens

Download mage2me
----------------

```bash
curl -L https://github.com/improper/mage2me/releases/latest/download/mage2me --output mage2me
chmod u+x ./mage2me
./mage2me --version
```

Download & Install Magento
--------------------------

Some of the options shown below are indeed optional. Checkout `./mage2me --help` for more info. 

```bash
./mage2me download output-directory \
    --mage-edition "Open Source" \
    --mage-version "2.3.5" \
    --github-token $GITHUB_TOKEN \
    --mage-access-key-public $MAGENTO_PUBLIC_KEY \
    --mage-access-key-private $MAGENTO_PRIVATE_KEY

cd magento-directory
composer install
```

List Download Options
---------------------

```bash
./mage2me --help
```


List Global Options
-------------------

```bash
./mage2me list
```
