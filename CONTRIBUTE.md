Contributing to FacebookConnector
=========================
## Bug Reports & Feature Requests
Before submitting bug reports and feature requests, please search through [open issues](https://github.com/boscho87/facebook-connector/issues) to see if yours has already been filed.

## Pull Requests
Pull requests should clearly describe the problem and solution. Include the relevant issue number if there is one.

## Code Style
Code should follow the PSR-2 Standard, here is the condesniffer configuration i use for the project.

```xml
<?xml version="1.0"?>
<ruleset name="IOTCloudStandard">
    <description>IOTCloudStandard.</description>
    <rule ref="PSR2"/>
    <rule ref="PSR1.Classes.ClassDeclaration.MultipleClasses">
        <severity>0</severity>
    </rule>
    <rule ref="Generic.Files.LineLength">
        <severity>0</severity>
    </rule>
    <rule ref="Squiz.Functions.MultiLineFunctionDeclaration.NewlineBeforeOpenBrace">
        <severity>0</severity>
    </rule>
    <rule ref="PSR2.Methods.FunctionCallSignature.ContentAfterOpenBracket">
        <severity>0</severity>
    </rule>
    <rule ref="Squiz.WhiteSpace.SuperfluousWhitespace" />
</ruleset>
``` 

<br>
This is a Free OpenSource Project, so please be friendly :)
