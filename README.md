# MODX3-Example
Bridging MODX3 to Extras using PHP Namespaces

_This is a working proof of concept. Use at your own risk. It is offered here to begin the conversation of best use case._

As MODx moves into version 3, there are several new things to get excited about. There are also items scheduled to be deprecated, as such, this example attempts to facilitate Namespace usage with minimal reliance on components such as `modx->getService()`.

## Directory Structure
I opted to use `core/components/CompanyName/ProjectName/Class.php` to house the primary class for the extra.

The  schema is stored in `core/components/CompanyName/ProjectName/model/schema`

The model is created in `core/components/CompanyName/ProjectName/model/Test`

The model namespace is `SanityLLC\\Example\\Test\\`

##XML Schema 
An example XML schema file is offered, though it is unknown at this time how `modUser` will be affected if and when MODX3 is entirely Namespaced.

**Things of note:**

- I chose to remove any prefix to the objects as the namespace will handle collisions.
- The `model` line uses a new version
- The `baseClass="xPDO\Om\xPDOObject"` uses namespace.
- The `package="\SanityLLC\Example\Test"` uses a namespace, which the parser heavily relies on to place the PHP class files.
- `<object class="Log" table="log" extends="xPDO\Om\xPDOSimpleObject">` also uses a names space.
- To replace the primary key, use : 
```
<object class="Log" table="client" extends="xPDO\Om\xPDOObject" comment="The Log.">
    <field key="id" dbtype="integer" precision="12" phptype="integer" attributes="unsigned" default="1" index="PRIMARY" comment="Primary Key." />
```
## The M3 worker class.
A base class for connecting the namespaced extra to modx.

**Things of note:**
- Make use of the shortcut functions `createPackageSchema()` and `createPackageTables()`
- The new `XPDOGenerator` parser function is quite extensive and can easily destroy your class files.
- I created shortcut functions that provide a layer of protection.
 
The following will need to be adjusted for your own projects:
```
    private $package = 'Test';
    /** @var string The namespace to prefix the schema objects. */
    private $packageNamespace = 'SanityLLC\\Example\\Test\\';
    /** @var string #var The actual filename of the schema. */
    private $schemaFileName = 'SanityLLC.Example';
    /** @var string $prefix The database table prefix for the package. */
    private $prefix = 'xx_';
```

The schema is create with a mixture of multiple settings:
```
 private function parsePackageSchema($compile = false, $regenerate = 0, $update = 2, $withNamespace = 1): bool
    {
        $options = array(
            'compile' => $compile,
            'namespacePrefix' => __NAMESPACE__,
            'outputDir' => $this->config['modelPath'] . $this->package,
            'regenerate' => $regenerate,
            'update' => $update,
            'withNamespace' => $withNamespace
        );
```

##How to use
- Clone the repository
- Copy it the `core` folder to match the structure herein.
- You should have a `siteroot/core/components/SanityLLC` directory when you are finished.
- Run the snippets contained in the `SanityLLC/Example/elements/snippets` directory in order.
- Expect errors due to logging level. Once the first two successfully run, the errors should no longer persist.

#Please fork and send requests.
My intention is provide the beginning of a coversation. I look forward to seeing contributions.



 

