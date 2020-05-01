# mage2me

Magento 2 Downloader for Commerce & Open Source

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
./mage2me output-directory \
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
