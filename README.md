# PPC

## Parser

```
creator(matcher, handler(mixed[]) -> mixed) -> parser(stream) -> mixed[]
```

## Combinator

```
creator(parser, handler(mixed[]) -> mixed) -> combinator(stream) -> mixed[]
```

## Control

```
creator(parser, handler(mixed[]) -> mixed) -> control(stream) -> mixed[]
```

## Handler

```
creator(option, handler(mixed[]) -> mixed) -> handler(mixed[]) -> mixed[]
```