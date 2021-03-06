--USE [new_sinkalmarlarnd]
GO
/****** Object:  StoredProcedure [dbo].[sp_viewsaldopettycash_lastinput]    Script Date: 2/6/2015 10:51:14 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[sp_viewsaldopettycash_lastinput]
@ID varchar(10),
@type int
AS
--sp_viewsaldopettycash_lastinput 2103

DECLARE @cek0 varchar(1), @cek1 varchar(1), @jumlah0 int, @jumlah1 int, @cek_last_opening_close int,@status_is_open int, @status_is_open_closing int, @cek_id int, @a int, @b int

select @jumlah0=count(status) from db_pettyclaim where status=0

select @cek_last_opening_close=MAX(pettycash_id) from db_pettyclaim where status in(1,2)

select @status_is_open_closing=ISNULL(MAX(pettycash_id), 0) from db_pettyclaim where pettycash_id > @cek_last_opening_close and status <> 2

select @status_is_open=count(pettycash_id) from db_pettyclaim where status not in (0,2) and pettycash_id < @ID

SET @a = @ID

SET @a = @a - 1

SET @b = 0

WHILE @b = 0
BEGIN
	select @cek_id=count(pettycash_id) from db_pettyclaim where pettycash_id = @a and status != 2
	IF @cek_id = 1
		BEGIN
			SET @b = 1
			BREAK
		END
	ELSE
		SET @a = @a - 1
		CONTINUE
END



IF @jumlah0=0
	BEGIN

		SELECT     isnull(saldo,0) as saldo
		FROM         dbo.db_pettyclaim
		WHERE     (status in (1, 2)) AND (pettycash_id =
								  (SELECT     MAX(pettycash_id) AS Expr1
									FROM          dbo.db_pettyclaim AS db_pettyclaim_1
									WHERE      (status in (1, 2))))
	END
ELSE
	BEGIN

		IF @status_is_open_closing = 0
			BEGIN
				SELECT     isnull(saldo,0) as saldo
				FROM         dbo.db_pettyclaim
				WHERE     (status in (1, 2)) AND (pettycash_id =
										  (SELECT     MAX(pettycash_id) AS Expr1
											FROM          dbo.db_pettyclaim AS db_pettyclaim_1
											WHERE      (status in (1, 2))))
			END
		ELSE
			BEGIN
				IF @type = 1
					BEGIN
						SELECT     isnull(saldo,0) as saldo
						FROM         dbo.db_pettyclaim
						WHERE     (status in (1, 2)) AND (pettycash_id =
												  (SELECT     MAX(pettycash_id) AS Expr1
													FROM          dbo.db_pettyclaim AS db_pettyclaim_1
													WHERE      (status in (1, 2))))
					END
				ELSE
					BEGIN
						SELECT     isnull(saldo,0) as saldo
						FROM         dbo.db_pettyclaim
						WHERE     (status <> 2) AND (pettycash_id = @a)
					END
				

			END
END                            
                            

                            
   
















