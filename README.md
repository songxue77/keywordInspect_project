# 2. 키워드 검수 프로그램 프로젝트

## 프로젝트 개요

네이버 마케팅 키워드 노출 현황 검수 프로그램

## 프로젝트 수행기간

2021.06.07 ~ 2021.07.09 (5주)

## 프로젝트 수행 상세

1. Laravel7(PHP7.2) 사용하여 Admin페이지 개발하고 네이버 검색페이지를 크롤링하는 로직은 PHP의 Goutte라이브러리 활용하였습니다.
2. 해당 시스템의 크롤링 요청이 동시다발적으로 발생하는게 아닌 특정 시간대(하루에 한번 사용한다고 전달받음)에만 발생하기 때문에 네이버에서 IP를 차단당할 리스크는 적다고 판단되어 IP를 바꿔가면서 크롤링할 필요는 없다고 결론을 내렸습니다.
3. 애플리케이션 로깅은 사이즈가 작은 시스템이기에 라라벨 로그 뷰어로도 충분하다고 생각하여 arcanedev/log-viewer패키지 설치하여 로그를 확인하였습니다. 기존에는 사용하던 rap2hpoutre/laravel-log-viewer패키지와 동일하게 /storage/logs 하위의 .log파일을 보여주는 기능이기때문에 큰 차이는 느끼지 못했습니다.
4. adminlte3 테마를 적용하였습니다. 라라벨에 쉽게 적용하기 위해 jeroennoten/laravel-adminlte패키지와 같이 사용하였습니다.
5. 리파지토리 패턴의 사용(prettus/l5-repository). 기존에 코드 구조에 관련해서 많은 고민을 하고 있었습니다. 기존에 저는 라라벨을 사용하면서 Controller, Service, Model로 로직을 분리하여 개발하였는데 다른 프로젝트에서 Repository를 사용한 코드들이 있어서 어떻게 사용하는지 찾아보았지만 사용이유를 잘 이해하지 못했습니다.  Laravel에서 쿼리를 실행하기 위해서는 다음의 몇가지 방법이 있습니다. 

5.1 DB파서드로 쿼리빌더를 직접 실행  
5.2 Model 인스턴스를 만들어서 쿼리빌더를 실행.  
5.3 Model 인스턴스를 만들어서 Eloquent로 실행하는 것입니다.

기존에는 두번째 방법을 많이 사용했었습니다. 쿼리를 실행하기 위해서는 Model인스턴스를 거쳐야 했고 Repository를 Service와 Model 중간에 배치하여도 Layer만 하나 추가되지 Model을 통해 쿼리를 실행하는건 차이가 없었습니다. 인터넷에서 리파지토리 패턴의 개념에 대해 알아보고나서야 'Repository를 통해 쿼리를 실행해준다'는 개념이 아니라는것을 깨달았습니다. 데이터 소스의 캡슐화 즉 데이터 출처와 관계없이 동일 인터페이스로 데이터에 접속할 수 있도록 만드는 것을 Repository패턴이라고 한다는 것을 해당 포스트에서 읽었습니다.

[[Android] Repository Pattern 의 이해](https://hs5555.tistory.com/112)

데이터 소스에 접근하기 위해 Model 앞에 Layer 한층을 추가한다는 개념보다 데이터 접근을 캡슐화하는게 포인트인 것입니다. 데이터 소스가 변동이 생겨도 기존 로직들은 변경할 필요가 없어서 프로그램을 더 유연하게 할 수 있습니다. 사실 현재 프로젝트에서 데이터소스가 변동이 생길 가능성은 거의 없다고 생각합니다. prettus/l5-repository패키지는 데이터 소스의 캡슐화도 있지만 데이터소스에 접근하기 쉽게 함수를 제공하고, 조회 데이터를 캐싱하고, DB에서 조회한 데이터를 View에 전달하기 위해 transform하는 기능에 집중되어 있습니다. 작은 프로젝트에는 꼭 적용할 필요는 없는 것 같습니다.

추가)
최근 요구사항이 하나 추가되면서 l5-repository의 단점을 하나 발견했습니다. update쿼리에 다중 where절을 추가할 수 없습니다. update(array $attribute, $primaryKey)로 정의되어있지만 단일 PK값으로만 업데이트가 가능한 것입이다. 어떻게 해결해야 할지 고민을 했습니다. 현재 테이블은 PK가 두개의 컬럼으로 지정되어있기 때문에 where조건 두개가 필요합니다. 결국 repository를 거치지 않고 Model을 직접 주입하여 Model인스턴스에 update쿼리를 날려서 데이터를 업데이트 하였습니다. l5-repository의 깃허브 issue에 해당 문제가 제기되었지만 아직 해결책은 없는 것 같습니다. 오픈소스 라이브러리를 사용할때 이러한 문제가 발생할 수 있다는걸 인지할 수 있게 되었습니다.

6. 테스트코드

admin의 CRUD로직을 모두 커버할 수 있는 만큼 테스트코드를 작성했습니다. 추가적인 고민은 다음과 같습니다.

6.1 Laravel Context를 같이 초기화하는 방식으로 Feature테스트는 모두 작성하였습니다. 그런데 Laravel Context를 초기화하지 않고 Unit테스트만 작성하려고 하니 어떤 부분을 어떻게 테스트해야 하는지 감이 잡히지 않았습니다. Container가 없으니 Model도 초기화할 수 없고, 서로서로 의존성이 묶여있는 코드의 한부분만 테스트하기란 거의 불가능했습니다.

6.2 또한 외부 API에 의존하는 로직(Naver API호출), 크롤링을 하는 로직들은 어떻게 테스트코드를 작성해야 하는지 모르겠습니다. 지속적으로 고민하고 테스트코드를 추가할 예정입니다.

## 느낀점

기존보다 고민을 많이 하면서 수행했던 프로젝트입니다.

repository패턴 적용, 테스트코드 작성, Admin 템플릿 적용

기존의 기술스텍을 갈아엎고 새로운 걸 시도할려고 했지만 조금 더 다른 생각을 하게 되었습니다. 새로운 기술스텍을 도입하는게 현재 회사의 상황에 맞는것일까? (Nodejs, SpringBoot, Python)을 도입하여 만들어낼 수 있다고 해도 현재 요구사항은 PHP, Mysql, JQuery로 대부분 개발이 가능합니다. 내가 다른 언어로 특정 시스템을 개발한다면 뒤에 인수받을 사람은 어떻게 생각할까. 내 발전만 중요하니 새로운 언어를 적용하고 퇴사 후 유지보수는 누가 하던지, 어떻게 하던지 큰 고민은 안해도 되는 것일까? 결국 이런걸 고민하면서 새로운 기술스텍 도입에 관해서 생각을 많이 하게 되었습니다. 그냥 PHP가 아니고 더 나아보이는 Java, SpringBoot를 도입하면 좋다가 아니라 회사의 이익에 부합되는 개발방향성을 생각하면서 개발을 해야 된다는 것을 알게 되었습니다. 이 모든게 다 정립되어있는 회사면 내가 매일 이런 고민을 안하고 개발에만 집중할 수 있어서 좋을지도 고민하게 되는 것 같습니다.

또한 코드를 github에 공개할려고 하니 기존에 하드코딩 되어있는 민감한 정보를 발견하였습니다. 개발하면서 코드를 오픈하기 위해 조금 더 신경써야 할 부분이 있다고 느꼈습니다.

## Github Repository Link

github.com/songxue77/keywordInspection_project
