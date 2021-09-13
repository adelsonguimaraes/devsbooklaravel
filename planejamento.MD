# Planejamento endpoints - API DEVSBOOK

## 1. Autenticação
* POST _api/auth/login (email, password)_
* POST _api/auth/logout_
* POST _api/auth/refresh_

## 2. Usuário
* POST _api/user/ (name, email, password, birthdate)_
* PUT _api/user (name, email, birthdate, city, work, password, password_confirm)_
* GET _api/user_
* GET _api/user/123_

### 2.1 Avatar
* POST _api/user/avatar (avatar)_
* POST _api/user/cover (cover)_

## 3. Feed
* GET _api/feed (page)_
* GET _api/user/feed (page)_
* GET _api/user/123/feed (page)_
* POST _api/feed (type=text/photo, body, photo)_

## 4. Postagem
* POST _api/post/123/like_
* POST _api/post/123/comment (txt)_

## 5. Search
* GET _api/search (txt)_