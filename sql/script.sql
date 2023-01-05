create table blog
(
    id          int auto_increment
        primary key,
    title       varchar(255) not null,
    description longtext     not null,
    date        datetime     not null,
    path_image  varchar(255) not null
)
    collate = utf8mb4_unicode_ci;

create table categorie
(
    id         int auto_increment
        primary key,
    name       varchar(255) not null,
    is_trend   tinyint(1)   not null,
    path_image varchar(255) not null
)
    collate = utf8mb4_unicode_ci;

create table doctrine_migration_versions
(
    version        varchar(191) not null
        primary key,
    executed_at    datetime     null,
    execution_time int          null
)
    collate = utf8_unicode_ci;

create table messenger_messages
(
    id           bigint auto_increment
        primary key,
    body         longtext     not null,
    headers      longtext     not null,
    queue_name   varchar(190) not null,
    created_at   datetime     not null,
    available_at datetime     not null,
    delivered_at datetime     null
)
    collate = utf8mb4_unicode_ci;

create index IDX_75EA56E016BA31DB
    on messenger_messages (delivered_at);

create index IDX_75EA56E0E3BD61CE
    on messenger_messages (available_at);

create index IDX_75EA56E0FB7336F0
    on messenger_messages (queue_name);

create table promotions
(
    id         int auto_increment
        primary key,
    date_start datetime not null,
    date_end   datetime not null,
    remise     double   not null
)
    collate = utf8mb4_unicode_ci;

create table produit
(
    id            int auto_increment
        primary key,
    promotions_id int          null,
    name          varchar(255) not null,
    price         double       not null,
    description   longtext     not null,
    path_image    varchar(255) not null,
    created_at    datetime     not null comment '(DC2Type:datetime_immutable)',
    is_trend      tinyint(1)   not null,
    is_available  tinyint(1)   not null,
    constraint FK_29A5EC2710007789
        foreign key (promotions_id) references promotions (id)
)
    collate = utf8mb4_unicode_ci;

create table categorie_produit
(
    categorie_id int not null,
    produit_id   int not null,
    primary key (categorie_id, produit_id),
    constraint FK_76264285BCF5E72D
        foreign key (categorie_id) references categorie (id)
            on delete cascade,
    constraint FK_76264285F347EFB
        foreign key (produit_id) references produit (id)
            on delete cascade
)
    collate = utf8mb4_unicode_ci;

create index IDX_76264285BCF5E72D
    on categorie_produit (categorie_id);

create index IDX_76264285F347EFB
    on categorie_produit (produit_id);

create index IDX_29A5EC2710007789
    on produit (promotions_id);

create table taille
(
    id     int auto_increment
        primary key,
    taille varchar(255) not null
)
    collate = utf8mb4_unicode_ci;

create table produit_by_size
(
    id         int auto_increment
        primary key,
    produit_id int not null,
    taille_id  int not null,
    stock      int not null,
    constraint FK_754F6AEF347EFB
        foreign key (produit_id) references produit (id),
    constraint FK_754F6AEFF25611A
        foreign key (taille_id) references taille (id)
)
    collate = utf8mb4_unicode_ci;

create index IDX_754F6AEF347EFB
    on produit_by_size (produit_id);

create index IDX_754F6AEFF25611A
    on produit_by_size (taille_id);

create table user
(
    id         int auto_increment
        primary key,
    name       varchar(255) not null,
    surname    varchar(255) not null,
    email      varchar(255) not null,
    password   varchar(255) not null,
    roles      json         not null,
    phone      varchar(30)  null,
    created_at datetime     not null comment '(DC2Type:datetime_immutable)',
    constraint UNIQ_8D93D649E7927C74
        unique (email)
)
    collate = utf8mb4_unicode_ci;

create table adresse
(
    id          int auto_increment
        primary key,
    user_id     int          not null,
    city        varchar(255) not null,
    rue         varchar(255) not null,
    postal_code int          not null,
    constraint FK_C35F0816A76ED395
        foreign key (user_id) references user (id)
)
    collate = utf8mb4_unicode_ci;

create index IDX_C35F0816A76ED395
    on adresse (user_id);

create table commande
(
    id            int auto_increment
        primary key,
    user_id       int          not null,
    date_facture  datetime     not null comment '(DC2Type:datetime_immutable)',
    etat_commande varchar(255) not null,
    constraint FK_6EEAA67DA76ED395
        foreign key (user_id) references user (id)
)
    collate = utf8mb4_unicode_ci;

create index IDX_6EEAA67DA76ED395
    on commande (user_id);

create table contact
(
    id          int auto_increment
        primary key,
    user_id     int          null,
    message     longtext     not null,
    date        datetime     not null,
    email       varchar(255) not null,
    phone       varchar(30)  null,
    name        varchar(255) not null,
    surname     varchar(255) not null,
    is_complete tinyint(1)   not null,
    constraint FK_4C62E638A76ED395
        foreign key (user_id) references user (id)
)
    collate = utf8mb4_unicode_ci;

create index IDX_4C62E638A76ED395
    on contact (user_id);

create table logs
(
    id      int auto_increment
        primary key,
    user_id int          not null,
    type    varchar(255) null,
    date    datetime     not null,
    constraint FK_F08FC65CA76ED395
        foreign key (user_id) references user (id)
)
    collate = utf8mb4_unicode_ci;

create index IDX_F08FC65CA76ED395
    on logs (user_id);

create table notation
(
    id         int auto_increment
        primary key,
    user_id    int    null,
    produit_id int    not null,
    note       double not null,
    constraint FK_268BC95A76ED395
        foreign key (user_id) references user (id),
    constraint FK_268BC95F347EFB
        foreign key (produit_id) references produit (id)
)
    collate = utf8mb4_unicode_ci;

create index IDX_268BC95A76ED395
    on notation (user_id);

create index IDX_268BC95F347EFB
    on notation (produit_id);

create table produit_in_commande
(
    id           int auto_increment
        primary key,
    produits_id  int    null,
    commandes_id int    null,
    quantite     int    not null,
    price        double not null,
    constraint FK_570B9EA58BF5C2E6
        foreign key (commandes_id) references commande (id),
    constraint FK_570B9EA5CD11A2CF
        foreign key (produits_id) references produit (id)
)
    collate = utf8mb4_unicode_ci;

create index IDX_570B9EA58BF5C2E6
    on produit_in_commande (commandes_id);

create index IDX_570B9EA5CD11A2CF
    on produit_in_commande (produits_id);

create table reset_password_request
(
    id           int auto_increment
        primary key,
    user_id      int          not null,
    selector     varchar(20)  not null,
    hashed_token varchar(100) not null,
    requested_at datetime     not null comment '(DC2Type:datetime_immutable)',
    expires_at   datetime     not null comment '(DC2Type:datetime_immutable)',
    constraint FK_7CE748AA76ED395
        foreign key (user_id) references user (id)
)
    collate = utf8mb4_unicode_ci;

create index IDX_7CE748AA76ED395
    on reset_password_request (user_id);

create table view_pages
(
    id    int auto_increment
        primary key,
    uri   varchar(255) not null,
    count int          not null
)
    collate = utf8mb4_unicode_ci;


