<div align="center">

# Fanyi

A PHP version of [afc163/fanyi](https://github.com/afc163/fanyi)

> A 🇨🇳 and 🇺🇸 translate tool in your command line.

![jaggle/fanyi](https://i.loli.net/2019/12/05/XkxtKRfSJumsIUQ.png)

</div>

## Install

```bash
$ composer global require jjsty1e/fanyi
```

> I suggest you to use [cgr](https://github.com/consolidation/cgr) to install this command to avoide dependices conflicts.

then add `~/.composer/vendor/bin` to `PATH` if you haven't yet.

## Usage

```bash
$ fanyi word
```

For short:

```bash
$ fy word
```

Translation data is fetched from [iciba.com](http://iciba.com) and [fanyi.youdao.com](http://fanyi.youdao.com), and only support translation between Chinese and English.

In Mac/Linux bash, words will be pronounced by `say` command.

Translate one word.

```bash
$ fanyi love
```

```bash
 love  英[ lʌv ] 美[ lʌv ]   ~  iciba.com

 - vt.& vi. 爱，热爱；爱戴；喜欢；赞美，称赞；
 - vt. 喜爱；喜好；喜欢；爱慕；
 - n. 爱情，爱意；疼爱；热爱；爱人，所爱之物；

 1. Love is the radical of lovely , loveliness , and loving.
    Love是lovely, loveliness 及loving的词根.
 2. She rhymes " love " with " dove ".
    她将 " love " 与 " dove " 两字押韵.
 3. In sports, love means nil.
    体育中, love的意思是零.
 4. It's been years since any hazardous - waste site as dramatic as Love Canal has been discovered.
    自Love运河中发现触目惊心的危险废料堆放场所以来,已过去多年.
 5. Is love, love, love oaths , evanescent love, absolutely love, love always can not escape this rule.
    是爱,示爱, 誓爱, 逝爱, 绝爱, 爱情始终逃不过这个规律.

  --------

 love  英[ lʌv ] 美[ lʌv ]   ~  fanyi.youdao.com

 - n. 爱；爱情；喜好；（昵称）亲爱的；爱你的；心爱的人；钟爱之物；零分
 - v. 爱恋（某人）；关爱；喜欢（某物或某事）；忠于
 - n. (Love) （英、菲、瑞、美）洛夫（人名）

 1. love
    爱, 爱情, 爱心
 2. Endless Love
    无尽的爱, 蓝色生死恋, 不了情
 3. puppy love
    早恋, 青春期恋爱, 初恋

  --------
```

More words.

```bash
$ fanyi make love
```

Support Chinese, even sentence.

```bash
$ fanyi 和谐
```

```bash
$ fanyi 那只敏捷的棕毛狐狸跃过那只懒狗
```
