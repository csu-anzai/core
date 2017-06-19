if "%1"=="" goto help
if "%2"=="" goto help
phpbench run ../../../core_agp/%1/tests/benchmarks/%2.php --report=aggregate --output=html > %2.html

:help
echo Usage: run.bat [module_calculations] [QueryVectorCacheBench]