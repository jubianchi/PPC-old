window.searchMetadata = [{"id":"0","name":"PPC\\CharStream","description":"","url":".\/ppc\/CharStream.html"},{"id":"1","name":"PPC\\Combinators\\chain","description":"\nChains several parsers and returns an array of the matched slices.\n\nThe chain will match only if all chained parsers match. It will stop and raise an error when a prser fails.\n\n## Example\n\n```php\nuse PPC\\CharStream;\nuse function PPC\\Combinators\\chain;\nuse function PPC\\Parsers\\is;\nuse function PPC\\Parsers\\not;\n\n$parser = chain([is('a'), not('a')]);\n$stream = new CharStream('abcd');\n\nassert($parser($stream) == [new \\PPC\\Slice(0, 1, $stream), new \\PPC\\Slice(1, 1, $stream)]);\nassert($stream->current() === 'c');\n```\n","url":".\/ppc\/combinators\/chain.html"},{"id":"2","name":"PPC\\Combinators\\repeat","description":"\n```php\nuse PPC\\CharStream;\nuse function PPC\\Combinators\\repeat;\nuse function PPC\\Parsers\\is;\n\n$parser = repeat(is('a'));\n$stream = new CharStream('aaabcd');\n\nassert($parser($stream) == [new \\PPC\\Slice(0, 1, $stream), new \\PPC\\Slice(1, 1, $stream), new \\PPC\\Slice(2, 1, $stream)]);\nassert($stream->current() === 'b');\n```\n","url":".\/ppc\/combinators\/repeat.html"},{"id":"3","name":"PPC\\Combinators\\optional","description":"\n```php\nuse PPC\\CharStream;\nuse function PPC\\Combinators\\optional;\nuse function PPC\\Parsers\\is;\n\n$parser = optional(is('a'));\n$stream = new CharStream('abcd');\n\nassert($parser($stream) == new \\PPC\\Slice(0, 1, $stream));\nassert($stream->current() === 'b');\n```\n","url":".\/ppc\/combinators\/optional.html"},{"id":"4","name":"PPC\\Combinators\\alternatives","description":"","url":".\/ppc\/combinators\/alternatives.html"},{"id":"5","name":"PPC\\Combinators\\boxed","description":"","url":".\/ppc\/combinators\/boxed.html"},{"id":"6","name":"PPC\\Combinators\\until","description":"","url":".\/ppc\/combinators\/until.html"},{"id":"7","name":"PPC\\Controls\\run","description":"","url":".\/ppc\/controls\/run.html"},{"id":"8","name":"PPC\\Controls\\call","description":"","url":".\/ppc\/controls\/call.html"},{"id":"9","name":"PPC\\Handlers\\toString","description":"","url":".\/ppc\/handlers\/toString.html"},{"id":"10","name":"PPC\\Handlers\\extract","description":"","url":".\/ppc\/handlers\/extract.html"},{"id":"11","name":"PPC\\Handlers\\flatMap","description":"","url":".\/ppc\/handlers\/flatMap.html"},{"id":"12","name":"PPC\\Handlers\\merge","description":"\n## Example\n\n```php\nuse PPC\\CharStream;\nuse function PPC\\Combinators\\chain;\nuse function PPC\\Handlers\\merge;\nuse function PPC\\Parsers\\is;\nuse function PPC\\Parsers\\not;\n\n$parser = chain([is('a'), not('a')], merge());\n$stream = new CharStream('abcd');\n\nassert($parser($stream) == new \\PPC\\Slice(0, 2, $stream));\nassert($stream->current() === 'c');\n```\n","url":".\/ppc\/handlers\/merge.html"},{"id":"13","name":"PPC\\Parsers\\is","description":"","url":".\/ppc\/parsers\/is.html"},{"id":"14","name":"PPC\\Parsers\\not","description":"","url":".\/ppc\/parsers\/not.html"},{"id":"15","name":"PPC\\Parsers\\regex","description":"","url":".\/ppc\/parsers\/regex.html"},{"id":"16","name":"PPC\\Parsers\\in","description":"","url":".\/ppc\/parsers\/in.html"},{"id":"17","name":"PPC\\Parsers\\eof","description":"","url":".\/ppc\/parsers\/eof.html"},{"id":"18","name":"PPC\\Parsers\\eol","description":"","url":".\/ppc\/parsers\/eol.html"},{"id":"19","name":"PPC\\Parsers\\space","description":"","url":".\/ppc\/parsers\/space.html"},{"id":"20","name":"PPC\\Parsers\\alpha","description":"","url":".\/ppc\/parsers\/alpha.html"},{"id":"21","name":"PPC\\Parsers\\numeric","description":"","url":".\/ppc\/parsers\/numeric.html"},{"id":"22","name":"PPC\\Parsers\\alnum","description":"","url":".\/ppc\/parsers\/alnum.html"},{"id":"23","name":"PPC\\Slice","description":"","url":".\/ppc\/Slice.html"}];