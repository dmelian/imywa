delimiter $$

drop procedure if exists sessions_create$$
create procedure sessions_create(
	in isessionId varchar(20)
)
begin
	insert into sessions(sessionId, usr, createTime, lastActiveTime, active) 
		values(isessionId, substring_index(user(),'@',1), now(), now(), true);

	select 'sessionApps' as resultId
		, userRoles.app
		, apps.dbServer
		, apps.source, appRoles.startClass
		, if(not appRoles.theme is null, appRoles.theme, apps.theme) as theme
		, appRoles.defPermissionType
		, userRoles.role
	from userRoles
		inner join appRoles on userRoles.app = appRoles.app and userRoles.role= appRoles.role 
		inner join apps on appRoles.app = apps.app
	where userRoles.usr = substring_index(user(),'@',1);

	select 'userConfig' as resultId
		, users.theme, users.language, users.defApp
	from users
	where users.usr = substring_index(user(),'@',1);

	select 0 as error, 'Mi sesión es la número $_sessionId' as message, isessionId as sessionId;
end
$$

drop procedure if exists sessions_getAppInfo$$
create procedure sessions_getAppInfo(
	in iapp varchar(20),
	in irole varchar(20)
)
begin
	select 'dbs' as resultId, appDbs.db, appDbs.main, if(appDbs.dbName is null, appDbs.db, appDbs.dbName) as dbName 
		, dbPartitions.partitionLevel, dbPartitions.partitionId	
		from appDbs left join dbPartitions on appDbs.db = dbPartitions.db
		where appDbs.app = iapp;
	select 'permissions' as resultId, objective, actions, permission from rolePermissions
		where app = iapp and role = irole;

	select 0 as error;
end
$$


delimiter ;


