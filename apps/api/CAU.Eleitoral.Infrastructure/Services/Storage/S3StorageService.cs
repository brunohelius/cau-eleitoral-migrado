using Amazon.S3;
using Amazon.S3.Model;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.Logging;

namespace CAU.Eleitoral.Infrastructure.Services.Storage;

public interface IS3StorageService
{
    Task<string> UploadAsync(Stream stream, string key, string contentType, CancellationToken ct = default);
    Task<Stream> DownloadAsync(string key, CancellationToken ct = default);
    Task DeleteAsync(string key, CancellationToken ct = default);
    Task<string> GetPresignedUrlAsync(string key, int expirationMinutes = 60);
}

public class S3StorageService : IS3StorageService
{
    private readonly IAmazonS3 _s3;
    private readonly string _bucketDocuments;
    private readonly ILogger<S3StorageService> _logger;

    public S3StorageService(IAmazonS3 s3, IConfiguration config, ILogger<S3StorageService> logger)
    {
        _s3 = s3;
        _bucketDocuments = config["AWS:S3:BucketDocuments"] ?? "cau-eleitoral-documents";
        _logger = logger;
    }

    public async Task<string> UploadAsync(Stream stream, string key, string contentType, CancellationToken ct = default)
    {
        var request = new PutObjectRequest
        {
            BucketName = _bucketDocuments,
            Key = key,
            InputStream = stream,
            ContentType = contentType,
            ServerSideEncryptionMethod = ServerSideEncryptionMethod.AES256
        };

        await _s3.PutObjectAsync(request, ct);
        _logger.LogInformation("Uploaded {Key} to S3 bucket {Bucket}", key, _bucketDocuments);
        return $"s3://{_bucketDocuments}/{key}";
    }

    public async Task<Stream> DownloadAsync(string key, CancellationToken ct = default)
    {
        var response = await _s3.GetObjectAsync(_bucketDocuments, key, ct);
        var ms = new MemoryStream();
        await response.ResponseStream.CopyToAsync(ms, ct);
        ms.Position = 0;
        return ms;
    }

    public async Task DeleteAsync(string key, CancellationToken ct = default)
    {
        await _s3.DeleteObjectAsync(_bucketDocuments, key, ct);
        _logger.LogInformation("Deleted {Key} from S3 bucket {Bucket}", key, _bucketDocuments);
    }

    public Task<string> GetPresignedUrlAsync(string key, int expirationMinutes = 60)
    {
        var request = new GetPreSignedUrlRequest
        {
            BucketName = _bucketDocuments,
            Key = key,
            Expires = DateTime.UtcNow.AddMinutes(expirationMinutes)
        };
        return Task.FromResult(_s3.GetPreSignedURL(request));
    }
}
