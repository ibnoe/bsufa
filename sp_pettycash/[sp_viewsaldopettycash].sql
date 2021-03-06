--USE [new_sinkalmarlarnd]
GO
/****** Object:  StoredProcedure [dbo].[sp_viewsaldopettycash]    Script Date: 2/6/2015 10:51:06 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
ALTER PROCEDURE [dbo].[sp_viewsaldopettycash]
@ID varchar(10)
AS


DECLARE @cek0 varchar(1), @cek1 varchar(1), @jumlah0 int, @jumlah1 int, @cek_last_opening_close int, @status_is_open_closing int


select @jumlah0=count(status) from db_pettyclaim where status=0

select @cek_last_opening_close=MAX(pettycash_id) from db_pettyclaim where status in(1,2)

select @status_is_open_closing=ISNULL(MAX(pettycash_id), 0) from db_pettyclaim where pettycash_id > @cek_last_opening_close and status <> 2


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
SELECT     isnull(saldo,0) as saldo
FROM         dbo.db_pettyclaim
WHERE     (status <> 2) AND (pettycash_id =
                          (SELECT     MAX(pettycash_id) AS Expr1
                            FROM          dbo.db_pettyclaim AS db_pettyclaim_1
                            WHERE      (status <> 2)))

END
END                            
                            
                            
                            
   
















