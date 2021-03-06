--USE [misnew]
GO
/****** Object:  StoredProcedure [dbo].[sp_appervendorx]    Script Date: 2/6/2015 8:12:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

ALTER proc [dbo].[sp_appervendor]

@pt VARCHAR(10),
@project VARCHAR(10),
@enddate DATETIME

AS
--exec sp_appervendorx '11','11101','2013-10-01','2014-10-20'
--sp_appervendorxs '11101','01-10-2013','2014-10-20'

--select * from tagihan
--select * from db_apinvoice order by apinvoice_id desc
--select * from db_apledger where malloc_amt is not null

IF (@pt = '11')

	BEGIN

		select a.project_no,b.nm_supplier as supplier, a.doc_no as nomorap, c.nm_project as project,inv_no as noinvoice,inv_date as tgl_inv,
		a.due_date as jatuhtempo,a.descs as uraian, (base_amt) as nilai,trx_amt as nilaibayar, 
		(mbase_amt - malloc_amt) as sisa,dpp_pph,percent_pph,(dpp_pph*3/100) as pph23,dpp_ppn,(a.dpp_ppn / 1.1) as ppn

		from db_apinvoice a

		LEFT JOIN pemasok b ON a.vendor_acct = b.kd_supplier
		LEFT JOIN project c ON a.project_no = c.kd_project
		LEFT JOIN db_apledger d on d.doc_no = a.doc_no
		--LEFT JOIN db_coa e ON a.pph_type = e.acc_no

		--select * from db_
		
		WHERE  a.doc_date <= @enddate and a.project_no = @project --AND a.flag <> 10

	END

ELSE IF (@pt = '22')

	BEGIN

		select b.nm_supplier as supplier, a.no_ap as nomorap, c.nm_project as project, noinvoice, tgl_inv, jatuhtempo, uraian, ((nilai+ppn)-pph23) as nilai, nilaibayar, nilaiadj,
		(((nilai+ppn)-pph23) - (nilaibayar+nilaiadj)) as sisa,dpp_pph,pph23,dpp_ppn,ppn

		from bdmall2005.dbo.tagihan a

		JOIN bdmall2005.dbo.pemasok b ON a.kd_supplier = b.kd_supplier
		JOIN bdmall2005.dbo.project c ON a.kd_project = c.kd_project

		WHERE  tgl_inv  <= @enddate AND a.user_hapus = 0

END







